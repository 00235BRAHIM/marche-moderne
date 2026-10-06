<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\VendorOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CustomerOrderStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public VendorOrder $vendorOrder,
        public string $event,
        public ?string $message = null
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $order = $this->vendorOrder->order;

        return [
            'type' => 'customer_order_status',
            'event' => $this->event,
            'vendor_order_id' => $this->vendorOrder->id,
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'vendor_order_number' => $this->vendorOrder->order_number,
            'total' => (float) $this->vendorOrder->total,
            'message' => $this->message,
            'created_at' => now()->toIso8601String(),
        ];
    }
}
