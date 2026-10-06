<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable implements CanResetPasswordContract {
    use Notifiable, CanResetPassword;
    protected $fillable=['name','email','password','phone','role','vendor_status','is_certified','certification_number','certification_document','nni','certified_at','shop_name','shop_slug','shop_logo','shop_description','facebook_url','youtube_url','whatsapp_url'];
    protected $hidden=['password','remember_token'];
    protected function casts():array{return ['email_verified_at'=>'datetime','password'=>'hashed','is_certified'=>'boolean','certified_at'=>'datetime'];}
    public function products(){return $this->hasMany(Product::class,'vendor_id');}
    public function paymentMethods(){return $this->hasMany(VendorPaymentMethod::class,'vendor_id');}
    public function manualPayments(){return $this->hasMany(ManualPaymentSubmission::class,'user_id');}
    public function cart(){return $this->hasOne(Cart::class);}
    public function orders(){return $this->hasMany(Order::class);}
    public function subscriptions(){return $this->hasMany(VendorSubscription::class,'vendor_id');}
    public function activeSubscription(){return $this->hasOne(VendorSubscription::class,'vendor_id')->active()->latestOfMany('ends_at');}
    public function isAdmin(){return in_array($this->role,['admin','super_admin'],true);}
    public function isSuperAdmin(){return $this->role==='super_admin';}
    public function isStaffAdmin(){return in_array($this->role,['admin','super_admin'],true);}
    public function isVendor(){return $this->role==='vendor';}
    public function isCertifiedVendor(){return $this->isVendor() && $this->vendor_status==='approved' && $this->is_certified;}
    public function hasActiveSubscription(){return $this->subscriptions()->active()->exists();}
    public function canSell(){return $this->isCertifiedVendor() && $this->hasActiveSubscription();}
}
