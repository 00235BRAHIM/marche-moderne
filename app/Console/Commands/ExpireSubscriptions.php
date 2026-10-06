<?php

namespace App\Console\Commands;

use App\Models\VendorSubscription;
use Illuminate\Console\Command;

class ExpireSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire';

    protected $description = 'Expire automatiquement les abonnements vendeurs arrivés à leur date de fin';

    public function handle()
    {
        $subscriptions = VendorSubscription::query()
            ->where('status', 'active')
            ->where('payment_status', 'paid')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now())
            ->get();

        if ($subscriptions->isEmpty()) {
            $this->info('Aucun abonnement à expirer.');
            return self::SUCCESS;
        }

        foreach ($subscriptions as $subscription) {
            $subscription->update([
                'status' => 'expired',
            ]);

            $this->line(
                "Abonnement #{$subscription->id} du vendeur #{$subscription->vendor_id} expiré automatiquement."
            );
        }

        $this->info(
            $subscriptions->count() . ' abonnement(s) expiré(s).'
        );

        return self::SUCCESS;
    }
}
