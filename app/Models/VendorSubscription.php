<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
class VendorSubscription extends Model {
    protected $fillable=['vendor_id','subscription_plan_id','starts_at','ends_at','status','payment_status','payment_method','reference','admin_note'];
    protected $casts=['starts_at'=>'datetime','ends_at'=>'datetime'];
    public function vendor(){return $this->belongsTo(User::class,'vendor_id');}
    public function plan(){return $this->belongsTo(SubscriptionPlan::class,'subscription_plan_id');}
    public function payments(){return $this->hasMany(PaymentTransaction::class);}
    public function isActive(): bool { return $this->status==='active' && $this->payment_status==='paid' && $this->ends_at && $this->ends_at->isFuture(); }
    public function scopeActive($query){return $query->where('status','active')->where('payment_status','paid')->where('ends_at','>',now());}
}
