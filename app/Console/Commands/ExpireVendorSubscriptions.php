<?php

namespace App\Console\Commands;

use App\Models\VendorSubscription;
use App\Models\Product;
use Illuminate\Console\Command;

class ExpireVendorSubscriptions extends Command
{
    protected $signature = "subscriptions:expire";

    protected $description = "Expire automatiquement les abonnements vendeurs et masque leurs produits";

    public function handle(): int
    {
        $subscriptions = VendorSubscription::query()
            ->where("status", "active")
            ->where("ends_at", "<=", now())
            ->get();

        foreach ($subscriptions as $subscription) {
            $subscription->update([
                "status" => "expired",
            ]);

            Product::where("vendor_id", $subscription->vendor_id)
                ->where("is_active", true)
                ->where("is_archived", false)
                ->update([
                    "was_active_before_subscription_expiry" => true,
                    "is_active" => false,
                ]);

            $this->info(
                "Abonnement #{$subscription->id} expiré : produits du vendeur #{$subscription->vendor_id} masqués."
            );
        }

        $this->info("Traitement terminé. {$subscriptions->count()} abonnement(s) expiré(s).");

        return self::SUCCESS;
    }
}
