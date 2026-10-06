<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SubscriptionPlan extends Model {
    protected $fillable=['name','description','price','duration_days','product_limit','is_active'];
    protected $casts=['price'=>'decimal:2','is_active'=>'boolean'];
    public function subscriptions(){return $this->hasMany(VendorSubscription::class);}
}
