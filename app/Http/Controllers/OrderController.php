<?php

namespace App\Http\Controllers;

use App\Models\{Order, VendorOrder, User};
use App\Notifications\NewVendorOrderNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function checkout()
    {
        return view('orders.checkout', [
            'cart' => auth()->user()
                ->cart()
                ->with('items.product.vendor')
                ->first()
        ]);
    }

    public function store(Request $r)
    {
        $d = $r->validate([
            'shipping_name' => 'required|max:120',
            'shipping_phone' => 'required|max:30',
            'shipping_address' => 'required|max:1000',
            'payment_method' => 'required|in:cash_on_delivery,manual_mobile_money',
            'notes' => 'nullable|max:1000',
        ]);

        $cart = auth()->user()
            ->cart()
            ->with('items.product')
            ->firstOrFail();

        abort_if(
            !$cart->items->count(),
            422,
            'Panier vide.'
        );

        $order = null;

        DB::transaction(function () use ($cart, $d, &$order) {

            foreach ($cart->items as $i) {
                abort_if(
                    $i->quantity > $i->product->stock,
                    422,
                    'Stock insuffisant pour ' . $i->product->name
                );
            }

            $groups = $cart->items->groupBy(
                fn ($i) => (int) $i->product->vendor_id
            );

            $sub = $cart->items->sum(
                fn ($i) => $i->unit_price * $i->quantity
            );

            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => 'SK-' . strtoupper(Str::random(8)),
                'subtotal' => $sub,
                'shipping_fee' => 0,
                'total' => $sub,
                'shipping_name' => $d['shipping_name'],
                'shipping_phone' => $d['shipping_phone'],
                'shipping_address' => $d['shipping_address'],
                'payment_method' => $d['payment_method'],
                'payment_status' => 'pending',
                'status' => 'pending',
                'notes' => $d['notes'] ?? null,
            ]);

            foreach ($groups as $vendorId => $vendorItems) {

                $vendorTotal = $vendorItems->sum(
                    fn ($i) => $i->unit_price * $i->quantity
                );

                $vendorOrder = VendorOrder::create([
                    'order_id' => $order->id,
                    'vendor_id' => $vendorId,
                    'order_number' => $order->order_number . '-V' . $vendorId,
                    'subtotal' => $vendorTotal,
                    'shipping_fee' => 0,
                    'total' => $vendorTotal,
                    'status' => 'pending',
                    'payment_status' => 'pending',
                ]);

                foreach ($vendorItems as $i) {
                    $order->items()->create([
                        'vendor_order_id' => $vendorOrder->id,
                        'product_id' => $i->product_id,
                        'product_name' => $i->product->name,
                        'quantity' => $i->quantity,
                        'unit_price' => $i->unit_price,
                        'total' => $i->unit_price * $i->quantity,
                    ]);

                }

                /*
                 * Notification vendeur :
                 * elle est envoyée uniquement après validation
                 * complète de la transaction SQL.
                 */
                $vendor = User::find($vendorId);

                if ($vendor && $vendor->isVendor()) {
                    DB::afterCommit(function () use ($vendor, $vendorOrder) {
                        $vendor->notify(
                            new NewVendorOrderNotification($vendorOrder)
                        );
                    });
                }
            }

            $cart->items()->delete();
        });

        return redirect()
            ->route('order.show', $order)
            ->with(
                'success',
                'Commande enregistrée et répartie automatiquement par vendeur.'
            );
    }

    public function index()
    {
        return view('orders.index', [
            'orders' => auth()
                ->user()
                ->orders()
                ->with(['vendorOrders.vendor.paymentMethods','vendorOrders.payments','vendorOrders.items.product'])
                ->latest()
                ->paginate(10)
        ]);
    }

    public function show(Order $order)
    {
        abort_unless(
            ((int) $order->user_id === (int) auth()->id())
            || auth()->user()->isAdmin(),
            403
        );

        $order->load([
            'items.product.vendor',
            'vendorOrders.vendor',
            'vendorOrders.items'
        ]);

        return view('orders.show', compact('order'));
    }
}
