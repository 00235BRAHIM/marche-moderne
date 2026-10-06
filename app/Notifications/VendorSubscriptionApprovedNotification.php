<?php

namespace App\Notifications;

use App\Models\VendorSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VendorSubscriptionApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public VendorSubscription $subscription
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $this->subscription->loadMissing('plan');

        return [
            'type' => 'vendor_subscription_approved',
            'event' => 'subscription_approved',
            'title' => 'Abonnement validé',
            'message' => 'Votre abonnement ' . ($this->subscription->plan->name ?? '') . ' a été validé par l’administrateur. Votre espace vendeur est maintenant disponible.',
            'subscription_id' => $this->subscription->id,
            'plan_name' => $this->subscription->plan->name ?? null,
            'url' => route('vendor.dashboard'),
        ];
    }
}
