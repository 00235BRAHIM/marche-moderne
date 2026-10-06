<?php
namespace App\Http\Controllers;
use App\Models\{PaymentTransaction,VendorSubscription};
use App\Services\Payments\PaymentManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class PaymentController extends Controller {
    public function pay(Request $request, VendorSubscription $subscription, PaymentManager $payments){
        abort_unless($subscription->vendor_id===auth()->id(),403);
        abort_unless($subscription->payment_status!=='paid',422,'Cet abonnement est déjà payé.');
        $data=$request->validate(['phone'=>'required|string|max:30']);
        try {
            $tx=$payments->create($subscription,$subscription->payment_method,$data['phone']);
            $result=$payments->gateway($subscription->payment_method)->initiate($tx);
            return redirect()->route('vendor.subscriptions')->with('success','Paiement '.$subscription->payment_method.' lancé. '.$result['message']);
        } catch(\Throwable $e){ Log::error('Payment initiation failed',['exception'=>$e,'subscription'=>$subscription->id]); return back()->withErrors(['payment'=>$e->getMessage()]); }
    }
    public function check(PaymentTransaction $transaction, PaymentManager $payments){
        abort_unless($transaction->user_id===auth()->id(),403);
        try {
            $result=$payments->gateway($transaction->provider)->status($transaction);
            if($result['status']==='paid') $payments->markPaid($transaction,$result['response']);
            elseif($result['status']==='failed') $payments->markFailed($transaction,'Paiement refusé.',$result['response']);
            return redirect()->route('vendor.subscriptions')->with('success',$result['status']==='paid'?'Paiement confirmé, abonnement activé.':'Statut du paiement : '.$result['status'].'.');
        } catch(\Throwable $e){ return back()->withErrors(['payment'=>$e->getMessage()]); }
    }
    public function webhook(Request $request,string $provider,PaymentManager $payments){
        $cfg=config('payments.'.($provider==='airtel_money'?'airtel':'moov'));
        $secret=$cfg['callback_secret'] ?? null;
        if($secret){
            $signature=$request->header('X-Signature') ?: $request->header('X-Webhook-Signature');
            $expected=hash_hmac('sha256',$request->getContent(),$secret);
            abort_unless($signature && hash_equals($expected,$signature),401);
        }
        $data=$request->all();
        $reference=$data['reference'] ?? data_get($data,'transaction.reference') ?? data_get($data,'data.reference');
        $external=$data['id'] ?? data_get($data,'transaction.id') ?? data_get($data,'data.transaction.id');
        $tx=PaymentTransaction::where('reference',$reference)->when($reference===null,fn($q)=>$q->where('external_id',$external))->first();
        if(!$tx) return response()->json(['ok'=>false,'message'=>'Transaction inconnue'],404);
        $raw=strtolower((string)($data['status'] ?? data_get($data,'transaction.status') ?? data_get($data,'data.transaction.status')));
        if(in_array($raw,['paid','success','successful','completed','ts'])) $payments->markPaid($tx,$data);
        elseif(in_array($raw,['failed','failure','cancelled','tf'])) $payments->markFailed($tx,'Paiement échoué.',$data);
        else $tx->update(['status'=>'pending','response_payload'=>$data,'external_id'=>$tx->external_id ?: $external]);
        return response()->json(['ok'=>true]);
    }
}
