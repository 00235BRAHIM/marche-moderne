<?php

namespace App\Http\Controllers;

use App\Models\VendorOrder;
use App\Models\ManualPaymentSubmission;
use Illuminate\Http\Request;
use App\Notifications\CustomerOrderStatusNotification;

class VendorOrderController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('q', ''));

        $orders = VendorOrder::where('vendor_id', auth()->id())
            ->with([
                'order.user',
                'items.product',
                'payments',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhereHas('order', function ($orderQuery) use ($search) {
                            $orderQuery
                                ->where('order_number', 'like', "%{$search}%")
                                ->orWhere('shipping_phone', 'like', "%{$search}%")
                                ->orWhereHas('user', function ($userQuery) use ($search) {
                                    $userQuery
                                        ->where('name', 'like', "%{$search}%")
                                        ->orWhere('email', 'like', "%{$search}%");
                                });
                        })
                        ->orWhereHas('items', function ($itemQuery) use ($search) {
                            $itemQuery->where('product_name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('vendor.orders.index', compact('orders', 'search'));
    }

    public function show(VendorOrder $vendorOrder)
    {
        abort_unless($vendorOrder->vendor_id == auth()->id(), 403);

        $vendorOrder->load([
            'order.user',
            'items.product',
            'payments',
        ]);

        return view('vendor.orders.show', compact('vendorOrder'));
    }

    public function approvePayment(
        Request $request,
        VendorOrder $vendorOrder,
        ManualPaymentSubmission $payment
    ) {
        abort_unless($vendorOrder->vendor_id == auth()->id(), 403);

        abort_unless(
            $payment->vendor_order_id == $vendorOrder->id,
            404
        );

        abort_if(
            $payment->status !== 'pending',
            422,
            'Cette preuve a déjà été traitée.'
        );

        abort_if(
            $vendorOrder->payment_status === 'paid',
            422,
            'Cette commande a déjà été payée et son stock a déjà été validé.'
        );

        /*
         * Le stock est diminué uniquement lorsque le paiement
         * est réellement validé par le vendeur.
         *
         * Les lignes paiement et commande sont verrouillées
         * pour empêcher une double validation simultanée.
         */
        \Illuminate\Support\Facades\DB::transaction(function () use ($payment, $vendorOrder, $request) {

            $lockedPayment = ManualPaymentSubmission::whereKey($payment->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_if(
                $lockedPayment->status !== 'pending',
                422,
                'Cette preuve a déjà été traitée.'
            );

            $lockedVendorOrder = VendorOrder::whereKey($vendorOrder->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_if(
                $lockedVendorOrder->payment_status === 'paid',
                422,
                'Cette commande a déjà été payée et son stock a déjà été validé.'
            );

            $lockedVendorOrder->load('items.product');

            $vendorOrder = $lockedVendorOrder;
            $payment = $lockedPayment;

            foreach ($vendorOrder->items as $item) {
                $product = $item->product()->lockForUpdate()->first();

                abort_if(
                    !$product,
                    422,
                    'Le produit "' . $item->product_name . '" n’existe plus.'
                );

                abort_if(
                    $item->quantity > $product->stock,
                    422,
                    'Stock insuffisant pour "' . $product->name .
                    '". Stock disponible : ' . $product->stock .
                    ', quantité commandée : ' . $item->quantity . '.'
                );
            }

            $payment->update([
                'status' => 'approved',
                'admin_note' => $request->input('note'),
                'validated_at' => now(),
                'validated_by' => auth()->id(),
            ]);

            foreach ($vendorOrder->items as $item) {
                $product = $item->product()->lockForUpdate()->first();

                $product->decrement('stock', $item->quantity);
            }

            $vendorOrder->update([
                'payment_status' => 'paid',
                'status' => 'processing',
            ]);
        });

        $order = $vendorOrder->order;

        $remaining = $order->vendorOrders()
            ->where('payment_status', '!=', 'paid')
            ->exists();

        if (!$remaining) {
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
            ]);
        }

        $order->user->notify(
            new CustomerOrderStatusNotification(
                $vendorOrder,
                'payment_approved',
                'Le paiement de votre commande a été validé par le vendeur.'
            )
        );

        return back()->with(
            'success',
            'Preuve de paiement acceptée. La commande est maintenant en préparation.'
        );
    }

    public function rejectPayment(
        Request $request,
        VendorOrder $vendorOrder,
        ManualPaymentSubmission $payment
    ) {
        abort_unless($vendorOrder->vendor_id == auth()->id(), 403);

        abort_unless(
            $payment->vendor_order_id == $vendorOrder->id,
            404
        );

        abort_if(
            $payment->status !== 'pending',
            422,
            'Cette preuve a déjà été traitée.'
        );

        $data = $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        $payment->update([
            'status' => 'rejected',
            'admin_note' => $data['note'],
            'validated_at' => now(),
            'validated_by' => auth()->id(),
        ]);

        $vendorOrder->update([
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);

        $order = $vendorOrder->order;

        $order->user->notify(
            new CustomerOrderStatusNotification(
                $vendorOrder,
                'payment_rejected',
                'Votre preuve de paiement a été refusée. Motif : ' . $data['note']
            )
        );

        return back()->with(
            'success',
            'Preuve de paiement refusée. Le client devra envoyer une nouvelle preuve.'
        );
    }
    /**
     * Accepter une commande avec paiement à la livraison.
     *
     * Le stock est réservé/diminué au moment où le vendeur
     * accepte réellement la commande.
     */
    public function acceptCashOnDelivery(VendorOrder $vendorOrder)
    {
        abort_unless(
            $vendorOrder->vendor_id == auth()->id(),
            403
        );

        abort_unless(
            $vendorOrder->order->payment_method === 'cash_on_delivery',
            422,
            'Cette action est uniquement disponible pour le paiement à la livraison.'
        );

        abort_if(
            $vendorOrder->payment_status === 'paid',
            422,
            'Cette commande est déjà payée.'
        );

        abort_if(
            $vendorOrder->status !== 'pending',
            422,
            'Cette commande a déjà été traitée.'
        );

        \Illuminate\Support\Facades\DB::transaction(function () use ($vendorOrder) {

            $lockedOrder = VendorOrder::whereKey($vendorOrder->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_if(
                $lockedOrder->status !== 'pending',
                422,
                'Cette commande a déjà été traitée.'
            );

            $lockedOrder->load('items.product');

            foreach ($lockedOrder->items as $item) {

                $product = $item->product()
                    ->lockForUpdate()
                    ->first();

                abort_if(
                    !$product,
                    422,
                    'Le produit "' . $item->product_name . '" n’existe plus.'
                );

                abort_if(
                    $item->quantity > $product->stock,
                    422,
                    'Stock insuffisant pour "' . $product->name .
                    '". Stock disponible : ' . $product->stock .
                    ', quantité commandée : ' . $item->quantity . '.'
                );
            }

            foreach ($lockedOrder->items as $item) {

                $product = $item->product()
                    ->lockForUpdate()
                    ->first();

                $product->decrement(
                    'stock',
                    $item->quantity
                );
            }

            $lockedOrder->update([
                'status' => 'processing',
                'payment_status' => 'pending',
            ]);

            $vendorOrder = $lockedOrder;
        });

        $order = $vendorOrder->order;

        $order->user->notify(
            new CustomerOrderStatusNotification(
                $vendorOrder,
                'order_accepted',
                'Votre commande a été acceptée par le vendeur. Elle sera payée à la livraison.'
            )
        );

        return back()->with(
            'success',
            'Commande acceptée. Le stock a été mis à jour et le paiement sera encaissé à la livraison.'
        );
    }

    /**
     * Marquer une commande vendeur comme expédiée.
     */
    public function ship(VendorOrder $vendorOrder)
    {
        abort_unless(
            $vendorOrder->vendor_id == auth()->id(),
            403
        );

        $isCashOnDelivery =
            $vendorOrder->order->payment_method === 'cash_on_delivery';

        if (!$isCashOnDelivery) {
            abort_if(
                $vendorOrder->payment_status !== 'paid',
                422,
                'La commande doit être payée avant expédition.'
            );
        }

        abort_if(
            $vendorOrder->status !== 'processing',
            422,
            'La commande doit être en préparation avant expédition.'
        );

        $vendorOrder->update([
            'status' => 'shipped',
        ]);

        $this->syncMainOrderStatus($vendorOrder);

        return back()->with(
            'success',
            'Commande marquée comme expédiée.'
        );
    }

    /**
     * Marquer une commande vendeur comme livrée.
     */
    public function deliver(VendorOrder $vendorOrder)
    {
        abort_unless(
            $vendorOrder->vendor_id == auth()->id(),
            403
        );

        abort_if(
            $vendorOrder->status !== 'shipped',
            422,
            'La commande doit être expédiée avant d’être livrée.'
        );

        $isCashOnDelivery =
            $vendorOrder->order->payment_method === 'cash_on_delivery';

        if (!$isCashOnDelivery) {
            abort_if(
                $vendorOrder->payment_status !== 'paid',
                422,
                'La commande doit être payée avant livraison.'
            );
        }

        $vendorOrder->update([
            'status' => 'delivered',
            'payment_status' => $isCashOnDelivery
                ? 'paid'
                : $vendorOrder->payment_status,
        ]);

        $this->syncMainOrderStatus($vendorOrder);

        return back()->with(
            'success',
            'Commande marquée comme livrée.'
        );
    }

    /**
     * Synchronise le statut de la commande principale
     * avec les commandes des différents vendeurs.
     */
    private function syncMainOrderStatus(VendorOrder $vendorOrder)
    {
        $order = $vendorOrder->order;

        $vendorOrders = $order->vendorOrders()->get();

        /*
         * Synchronisation du paiement principal.
         *
         * La commande principale est payée uniquement lorsque
         * TOUS les vendeurs ont reçu leur paiement.
         */
        $allPaid = $vendorOrders->every(
            fn ($vo) => $vo->payment_status === 'paid'
        );

        $order->update([
            'payment_status' => $allPaid ? 'paid' : 'pending',
        ]);

        /*
         * Synchronisation du statut de livraison.
         */
        if ($vendorOrders->every(
            fn ($vo) => $vo->status === 'delivered'
        )) {
            $order->update([
                'status' => 'delivered',
            ]);

            return;
        }

        if ($vendorOrders->every(
            fn ($vo) => in_array(
                $vo->status,
                ['shipped', 'delivered'],
                true
            )
        )) {
            $order->update([
                'status' => 'shipped',
            ]);

            return;
        }

        if ($vendorOrders->contains(
            fn ($vo) => in_array(
                $vo->status,
                ['processing', 'shipped', 'delivered'],
                true
            )
        )) {
            $order->update([
                'status' => 'processing',
            ]);

            return;
        }

        $order->update([
            'status' => 'pending',
        ]);
    }
}
