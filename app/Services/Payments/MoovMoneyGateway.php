<?php
namespace App\Services\Payments;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Http;
use RuntimeException;
class MoovMoneyGateway implements PaymentGateway {
    private function client(){
        $cfg=config('payments.moov');
        if(!$cfg['enabled'] || !$cfg['base_url'] || !$cfg['api_key']) throw new RuntimeException('Moov Money n’est pas configuré. Les identifiants de l’intégration partenaire sont requis.');
        return Http::acceptJson()->withToken($cfg['api_key']);
    }
    public function initiate(PaymentTransaction $transaction): array {
        $cfg=config('payments.moov');
        $payload=['reference'=>$transaction->reference,'amount'=>(float)$transaction->amount,'currency'=>$cfg['currency'],'customer'=>['phone'=>$transaction->phone],'merchant_id'=>$cfg['merchant_id'],'callback_url'=>route('payments.webhook',['provider'=>'moov_money'])];
        $response=$this->client()->post(rtrim($cfg['base_url'],'/').$cfg['collection_path'],$payload);
        $json=$response->json();
        if(!$response->successful()) throw new RuntimeException($json['message'] ?? 'Moov Money a refusé la demande de paiement.');
        $transaction->update(['external_id'=>$json['id'] ?? $json['transaction_id'] ?? null,'status'=>'pending','request_payload'=>$payload,'response_payload'=>$json]);
        return ['status'=>'pending','external_id'=>$transaction->external_id,'message'=>$json['message'] ?? 'Demande de paiement envoyée.'];
    }
    public function status(PaymentTransaction $transaction): array {
        $cfg=config('payments.moov');
        $path=str_replace('{id}',urlencode($transaction->external_id),$cfg['status_path']);
        $response=$this->client()->get(rtrim($cfg['base_url'],'/').$path);
        $json=$response->json();
        if(!$response->successful()) throw new RuntimeException($json['message'] ?? 'Impossible de vérifier le paiement Moov Money.');
        $raw=strtolower((string)($json['status'] ?? $json['payment_status'] ?? 'pending'));
        $status=in_array($raw,['paid','success','successful','completed'])?'paid':(in_array($raw,['failed','failure','cancelled'])?'failed':'pending');
        return ['status'=>$status,'response'=>$json];
    }
}
