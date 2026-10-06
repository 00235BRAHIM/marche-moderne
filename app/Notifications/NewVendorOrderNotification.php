<?php

namespace App\Notifications;

use App\Models\VendorOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewVendorOrderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public VendorOrder $vendorOrder
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'new_vendor_order',
            'vendor_order_id' => $this->vendorOrder->id,
            'order_number' => $this->vendorOrder->order_number,
            'total' => (float) $this->vendorOrder->total,
            'customer_name' => $this->vendorOrder->order->shipping_name,
            'created_at' => $this->vendorOrder->created_at?->toIso8601String(),
        ];
    }
}
