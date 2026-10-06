<?php
namespace App\Services\Payments;
use App\Models\PaymentTransaction;
use App\Models\VendorSubscription;
use Illuminate\Support\Str;
use RuntimeException;
class PaymentManager {
    public function gateway(string $provider): PaymentGateway {
        return match($provider){
            'airtel_money'=>app(AirtelMoneyGateway::class),
            'moov_money'=>app(MoovMoneyGateway::class),
            default=>throw new RuntimeException('Fournisseur de paiement non pris en charge.')
        };
    }
    public function create(VendorSubscription $subscription,string $provider,string $phone): PaymentTransaction {
        return PaymentTransaction::create(['vendor_subscription_id'=>$subscription->id,'user_id'=>$subscription->vendor_id,'provider'=>$provider,'reference'=>'PAY-'.strtoupper(Str::random(14)),'phone'=>$phone,'amount'=>$subscription->plan->price,'currency'=>config('payments.currency'),'status'=>'initiated']);
    }
    public function markPaid(PaymentTransaction $transaction,array $response=[]): void {
        $transaction->update(['status'=>'paid','paid_at'=>now(),'response_payload'=>$response]);
        $subscription=$transaction->subscription()->with('plan')->firstOrFail();
        $start=now();
        $subscription->update(['starts_at'=>$start,'ends_at'=>$start->copy()->addDays($subscription->plan->duration_days),'status'=>'active','payment_status'=>'paid','payment_method'=>$transaction->provider,'reference'=>$subscription->reference ?: $transaction->reference]);
    }
    public function markFailed(PaymentTransaction $transaction,string $reason,array $response=[]): void {
        $transaction->update(['status'=>'failed','failure_reason'=>$reason,'response_payload'=>$response]);
        $transaction->subscription()->update(['payment_status'=>'failed']);
    }
}
