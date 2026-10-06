<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class VendorOrder extends Model {
 protected $fillable=['order_id','vendor_id','order_number','subtotal','shipping_fee','total','status','payment_status'];
 protected $casts=['subtotal'=>'decimal:2','shipping_fee'=>'decimal:2','total'=>'decimal:2'];
 public function order(){return $this->belongsTo(Order::class);}
 public function vendor(){return $this->belongsTo(User::class,'vendor_id');}
 public function items(){return $this->hasMany(OrderItem::class);}
 public function payments(){return $this->hasMany(ManualPaymentSubmission::class);}
}
