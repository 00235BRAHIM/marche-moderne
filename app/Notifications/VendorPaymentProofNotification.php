<?php

namespace App\Notifications;

use App\Models\ManualPaymentSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VendorPaymentProofNotification extends Notification
{
    use Queueable;

    public function __construct(
        public ManualPaymentSubmission $payment
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $vendorOrder = $this->payment->vendorOrder;
        $order = $vendorOrder->order;

        return [
            'type' => 'vendor_payment_proof',
            'event' => 'payment_proof_submitted',
            'vendor_order_id' => $vendorOrder->id,
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'vendor_order_number' => $vendorOrder->order_number,
            'customer_name' => $order->shipping_name,
            'amount' => (float) $this->payment->amount,
            'provider' => $this->payment->provider,
            'created_at' => now()->toIso8601String(),
        ];
    }
}
