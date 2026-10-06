<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PaymentTransaction extends Model {
    protected $fillable = ['vendor_subscription_id','user_id','provider','external_id','reference','phone','amount','currency','status','failure_reason','request_payload','response_payload','paid_at'];
    protected $casts = ['amount'=>'decimal:2','request_payload'=>'array','response_payload'=>'array','paid_at'=>'datetime'];
    public function subscription(){ return $this->belongsTo(VendorSubscription::class,'vendor_subscription_id'); }
    public function user(){ return $this->belongsTo(User::class); }
}
