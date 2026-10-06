<?php
namespace App\Services\Payments;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
class AirtelMoneyGateway implements PaymentGateway {
    private function token(): string {
        $cfg=config('payments.airtel');
        $response=Http::asForm()->acceptJson()->withBasicAuth($cfg['client_id'], $cfg['client_secret'])->post(rtrim($cfg['base_url'],'/').'/auth/oauth2/token',['grant_type'=>'client_credentials']);
        if(!$response->successful() || !$response->json('access_token')) throw new RuntimeException('Impossible d’obtenir le jeton Airtel Money.');
        return $response->json('access_token');
    }
    public function initiate(PaymentTransaction $transaction): array {
        $cfg=config('payments.airtel');
        if(!$cfg['enabled']) throw new RuntimeException('Airtel Money n’est pas activé dans la configuration.');
        $id=(string)Str::uuid();
        $payload=['reference'=>$transaction->reference,'subscriber'=>['country'=>$cfg['country'],'currency'=>$cfg['currency'],'msisdn'=>preg_replace('/\D+/','',$transaction->phone)],'transaction'=>['amount'=>(float)$transaction->amount,'country'=>$cfg['country'],'currency'=>$cfg['currency'],'id'=>$id]];
        $response=Http::acceptJson()->withToken($this->token())->withHeaders(['X-Country'=>$cfg['country'],'X-Currency'=>$cfg['currency']])->post(rtrim($cfg['base_url'],'/').$cfg['collection_path'],$payload);
        $json=$response->json();
        if(!$response->successful()) throw new RuntimeException($json['message'] ?? 'Airtel Money a refusé la demande de paiement.');
        $transaction->update(['external_id'=>$json['data']['transaction']['id'] ?? $id,'status'=>'pending','request_payload'=>$payload,'response_payload'=>$json]);
        return ['status'=>'pending','external_id'=>$transaction->external_id,'message'=>$json['message'] ?? 'Demande de paiement envoyée.'];
    }
    public function status(PaymentTransaction $transaction): array {
        $cfg=config('payments.airtel');
        $path=str_replace('{id}',urlencode($transaction->external_id),$cfg['status_path']);
        $response=Http::acceptJson()->withToken($this->token())->withHeaders(['X-Country'=>$cfg['country'],'X-Currency'=>$cfg['currency']])->get(rtrim($cfg['base_url'],'/').$path);
        $json=$response->json();
        if(!$response->successful()) throw new RuntimeException($json['message'] ?? 'Impossible de vérifier le paiement Airtel Money.');
        $raw=strtoupper((string)($json['data']['transaction']['status'] ?? $json['status'] ?? 'PENDING'));
        $status=match($raw){'TS','SUCCESS','COMPLETED','PAID'=>'paid','TF','FAILED','FAILURE'=>'failed',default=>'pending'};
        return ['status'=>$status,'response'=>$json];
    }
}
