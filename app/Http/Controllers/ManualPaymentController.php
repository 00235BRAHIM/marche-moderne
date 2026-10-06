<?php

namespace App\Http\Controllers;

use App\Support\AdminNotifier;
use App\Models\{
    ManualPaymentSubmission,
    VendorSubscription,
    Order,
    VendorPaymentMethod,
    VendorOrder
};
use App\Notifications\VendorPaymentProofNotification;

use Illuminate\Http\Request;

class ManualPaymentController extends Controller
{
    /**
     * Paiement de l'abonnement vendeur.
     * Les numéros affichés sont ceux de l'administrateur.
     */
    public function subscriptionForm(VendorSubscription $subscription)
    {
        abort_unless(
            $subscription->vendor_id == auth()->id(),
            403
        );

        abort_if(
            $subscription->payment_status === 'paid',
            422,
            'Cet abonnement est déjà payé.'
        );

        $methods = collect(config('payment.admin.methods', []));

        return view(
            'vendor.subscription-payment',
            compact('subscription', 'methods')
        );
    }

    /**
     * Envoi de la preuve de paiement de l'abonnement.
     */
    public function subscriptionSubmit(
        Request $request,
        VendorSubscription $subscription
    ) {
        abort_unless(
            $subscription->vendor_id == auth()->id(),
            403
        );

        abort_if(
            $subscription->payment_status === 'paid',
            422,
            'Cet abonnement est déjà payé.'
        );

        $data = $request->validate([
            'provider' => 'required|in:airtel_money,moov_money',
            'payer_name' => 'required|max:150',
            'transaction_reference' => 'nullable|max:120',
            'screenshot' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        /*
         * Recherche du numéro ADMIN correspondant
         * au moyen de paiement choisi.
         */
        $method = collect(config('payment.admin.methods', []))
            ->firstWhere('provider', $data['provider']);

        abort_unless($method, 422, 'Moyen de paiement administrateur introuvable.');

        abort_if(
            empty($method['transfer_number']),
            422,
            "Le numéro de paiement administrateur n''est pas configuré."
        );

        $path = $request
            ->file('screenshot')
            ->store('manual-payments', 'public');

        ManualPaymentSubmission::create([
            'user_id' => auth()->id(),
            'vendor_id' => auth()->id(),
            'vendor_subscription_id' => $subscription->id,
            'provider' => $data['provider'],
            'transfer_number' => $method['transfer_number'],
            'payer_name' => $data['payer_name'],
            'transaction_reference' => $data['transaction_reference'] ?? null,
            'amount' => $subscription->plan->price,
            'screenshot_path' => $path,
        ]);

        AdminNotifier::send(
            'Nouvelle preuve de paiement',
            auth()->user()->name . ' a envoyé une preuve de paiement pour son abonnement.',
            '💳',
            route('admin.payments')
        );

        $subscription->update([
            'payment_status' => 'pending',
        ]);

        return redirect()
            ->route('vendor.subscriptions')
            ->with(
                'success',
                'Preuve envoyée. L’administrateur doit vérifier le transfert avant activation.'
            );
    }

    /**
     * Page de paiement d'une commande.
     *
     * Ici les numéros sont ceux des vendeurs.
     */
    public function orderForm(Order $order)
    {
        abort_unless(
            (int) $order->user_id === (int) auth()->id(),
            403
        );

        $order->load([
            'vendorOrders.vendor.paymentMethods',
            'vendorOrders.items'
        ]);

        $groups = $order->vendorOrders->mapWithKeys(
            fn ($vo) => [
                $vo->id => [
                    'vendorOrder' => $vo,
                    'vendor' => $vo->vendor,
                    'total' => $vo->total,
                    'methods' => $vo->vendor->paymentMethods
                        ->where('is_active', true),
                ]
            ]
        );

        return view(
            'orders.manual-payment',
            compact('order', 'groups')
        );
    }

    /**
     * Envoi de la preuve de paiement d'une commande.
     *
     * Le paiement est envoyé au vendeur concerné.
     */
    public function orderSubmit(Request $request, Order $order)
    {
        abort_unless(
            (int) $order->user_id === (int) auth()->id(),
            403
        );

        $data = $request->validate([
            'vendor_order_id' => 'required|exists:vendor_orders,id',
            'provider' => 'required|in:airtel_money,moov_money',
            'payer_name' => 'required|max:150',
            'transaction_reference' => 'nullable|max:120',
            'screenshot' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $vendorOrder = VendorOrder::where('id', $data['vendor_order_id'])
            ->where('order_id', $order->id)
            ->firstOrFail();

        abort_if(
            $vendorOrder->payment_status === 'paid',
            422,
            'Ce vendeur est déjà payé pour cette commande.'
        );

        /*
         * Ici on utilise bien le numéro du VENDEUR.
         */
        $method = VendorPaymentMethod::where(
                'vendor_id',
                $vendorOrder->vendor_id
            )
            ->where('provider', $data['provider'])
            ->where('is_active', true)
            ->firstOrFail();

        $already = ManualPaymentSubmission::where(
                'vendor_order_id',
                $vendorOrder->id
            )
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        abort_if(
            $already,
            422,
            'Une preuve est déjà en cours de traitement pour ce vendeur.'
        );

        $path = $request
            ->file('screenshot')
            ->store('manual-payments', 'public');

        $payment = ManualPaymentSubmission::create([
            'user_id' => auth()->id(),
            'vendor_id' => $vendorOrder->vendor_id,
            'order_id' => $order->id,
            'vendor_order_id' => $vendorOrder->id,
            'provider' => $data['provider'],
            'transfer_number' => $method->transfer_number,
            'payer_name' => $data['payer_name'],
            'transaction_reference' => $data['transaction_reference'] ?? null,
            'amount' => $vendorOrder->total,
            'screenshot_path' => $path,
        ]);

        $vendor = $vendorOrder->vendor;

        if ($vendor) {
            $vendor->notify(
                new VendorPaymentProofNotification($payment)
            );
        }

        return redirect()
            ->route('order.payment', $order)
            ->with(
                'success',
                'Preuve envoyée pour ' . $vendorOrder->vendor->name . '.'
            );
    }
}
