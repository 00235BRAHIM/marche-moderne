<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ManualPaymentSubmission extends Model {
 protected $fillable=['user_id','vendor_id','vendor_subscription_id','order_id','vendor_order_id','provider','transfer_number','payer_name','transaction_reference','amount','currency','screenshot_path','status','admin_note','validated_by','validated_at'];
 protected $casts=['amount'=>'decimal:2','validated_at'=>'datetime'];
 public function user(){return $this->belongsTo(User::class);}
 public function vendor(){return $this->belongsTo(User::class,'vendor_id');}
 public function subscription(){return $this->belongsTo(VendorSubscription::class,'vendor_subscription_id');}
 public function order(){return $this->belongsTo(Order::class);}
 public function vendorOrder(){return $this->belongsTo(VendorOrder::class);}
 public function validator(){return $this->belongsTo(User::class,'validated_by');}
}
