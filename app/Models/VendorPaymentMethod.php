<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class VendorPaymentMethod extends Model {
 protected $fillable=['vendor_id','provider','account_name','transfer_number','is_active'];
 protected $casts=['is_active'=>'boolean'];
 public function vendor(){return $this->belongsTo(User::class,'vendor_id');}
 public function label(): string { return $this->provider==='airtel_money'?'Airtel Money':'Moov Money'; }
}
