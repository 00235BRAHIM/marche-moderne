<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicShopController;
use App\Http\Controllers\{HomeController,AuthController,CartController,OrderController,DashboardController,VendorProductController,ManualPaymentController,VendorOrderController,VendorNotificationController,CustomerNotificationController};
use App\Http\Controllers\Vendor\SubscriptionController;
use App\Http\Controllers\Vendor\ShopController;
use App\Http\Controllers\Vendor\PaymentSettingsController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminNotificationController;
Route::get('/',[HomeController::class,'index'])->name('home');
Route::get('/products',[HomeController::class,'products'])->name('products');
Route::get('/shops', [PublicShopController::class, 'index'])->name('shops.index');
Route::get('/shop/{slug}', [PublicShopController::class, 'show'])->name('shop.public');
Route::get('/products/{product}',[HomeController::class,'show'])->name('product.show');
Route::middleware('guest')->group(function(){Route::get('/login',[AuthController::class,'loginForm'])->name('login');Route::post('/login',[AuthController::class,'login']);Route::get('/register',[AuthController::class,'registerForm'])->name('register');Route::post('/register',[AuthController::class,'register']);});

Route::get('/forgot-password', [AuthController::class, 'forgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'resetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
Route::middleware('auth')->group(function(){
    Route::post('/logout',[AuthController::class,'logout'])->name('logout');
    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');
    Route::get('/client/dashboard',[DashboardController::class,'client'])->name('client.dashboard');
    Route::get('/cart',[CartController::class,'index'])->name('cart');
    Route::post('/cart/{product}',[CartController::class,'add'])->name('cart.add');Route::patch('/cart/{item}',[CartController::class,'update'])->name('cart.update');Route::delete('/cart/{item}',[CartController::class,'remove'])->name('cart.remove');
    Route::get('/checkout',[OrderController::class,'checkout'])->name('checkout');Route::post('/orders',[OrderController::class,'store'])->name('orders.store');Route::get('/orders',[OrderController::class,'index'])->name('orders');Route::get('/orders/{order}',[OrderController::class,'show'])->name('order.show');Route::get('/orders/{order}/payment',[ManualPaymentController::class,'orderForm'])->name('order.payment');Route::post('/orders/{order}/payment',[ManualPaymentController::class,'orderSubmit'])->name('order.payment.submit');
    Route::get('/notifications',[CustomerNotificationController::class,'index'])->name('notifications');
    Route::post('/notifications/{id}/read',[CustomerNotificationController::class,'read'])->name('notifications.read');
    Route::post('/notifications/read-all',[CustomerNotificationController::class,'readAll'])->name('notifications.read-all');

    Route::prefix('vendor')->name('vendor.')->middleware('role:vendor')->group(function(){
        Route::get('/shop', [ShopController::class, 'edit'])->name('shop.edit');
        Route::post('/shop', [ShopController::class, 'update'])->name('shop.update');

        Route::get('/dashboard',[DashboardController::class,'vendor'])->name('dashboard');
        Route::get('/notifications',[VendorNotificationController::class,'index'])->name('notifications');

        Route::post('/notifications/{id}/read',[VendorNotificationController::class,'read'])->name('notifications.read');

        Route::post('/notifications/read-all',[VendorNotificationController::class,'readAll'])->name('notifications.read-all');

	Route::get('/orders',[VendorOrderController::class,'index'])->name('orders.index');
	Route::get('/orders/{vendorOrder}',[VendorOrderController::class,'show'])->name('orders.show');
	Route::post('/orders/{vendorOrder}/payments/{payment}/approve',[VendorOrderController::class,'approvePayment'])->name('orders.payments.approve');
	Route::post('/orders/{vendorOrder}/payments/{payment}/reject',[VendorOrderController::class,'rejectPayment'])->name('orders.payments.reject');
	Route::post('/orders/{vendorOrder}/accept-cod', [VendorOrderController::class, 'acceptCashOnDelivery'])->name('orders.accept-cod');
	Route::post('/orders/{vendorOrder}/ship', [VendorOrderController::class, 'ship']) ->name('orders.ship');
	Route::post('/orders/{vendorOrder}/deliver', [VendorOrderController::class, 'deliver'])->name('orders.deliver');
        Route::get('/certification',[SubscriptionController::class,'certification'])->name('certification');
        Route::post('/certification',[SubscriptionController::class,'submitCertification'])->name('certification.submit');
        Route::get('/subscriptions',[SubscriptionController::class,'index'])->name('subscriptions');
        Route::post('/subscriptions',[SubscriptionController::class,'store'])->name('subscriptions.store');
        Route::get('/subscriptions/{subscription}/payment',[ManualPaymentController::class,'subscriptionForm'])->name('subscriptions.payment');
        Route::post('/subscriptions/{subscription}/payment',[ManualPaymentController::class,'subscriptionSubmit'])->name('subscriptions.payment.submit');
        Route::get('/payment-settings',[PaymentSettingsController::class,'index'])->name('payment-settings');
        Route::post('/payment-settings',[PaymentSettingsController::class,'save'])->name('payment-settings.save');
        Route::middleware('vendor.access')->group(function(){Route::resource('products',VendorProductController::class)->except('show');});
    });
    
    Route::prefix("admin/super-admin")->name("admin.super-admin.")->middleware("super_admin")->group(function(){
        Route::get("/users", [\App\Http\Controllers\Admin\SuperAdminController::class, "users"])->name("users");
        Route::put("/users/{user}/role", [\App\Http\Controllers\Admin\SuperAdminController::class, "updateRole"])->name("users.role");
        Route::delete("/users/{user}", [\App\Http\Controllers\Admin\SuperAdminController::class, "destroy"])->name("users.destroy");
    });

Route::prefix('admin')->name('admin.')->middleware('role:admin,super_admin')->group(function(){
        Route::get('/dashboard',[AdminController::class,'dashboard'])->name('dashboard');
        Route::post('/notifications/{id}/read',[AdminNotificationController::class,'read'])->name('notifications.read');
        Route::post('/notifications/read-all',[AdminNotificationController::class,'readAll'])->name('notifications.read-all');
        Route::get('/vendors',[AdminController::class,'vendors'])->name('vendors');
        Route::post('/vendors/{user}/certify',[AdminController::class,'certify'])->name('vendors.certify');
        Route::get('/subscriptions',[AdminController::class,'subscriptions'])->name('subscriptions');
        Route::post('/subscriptions/{subscription}',[AdminController::class,'updateSubscription'])->name('subscriptions.update');
        Route::get('/payments',[AdminController::class,'payments'])->name('payments');
        Route::post('/payments/{payment}',[AdminController::class,'validatePayment'])->name('payments.validate');
        Route::get('/plans',[AdminController::class,'plans'])->name('plans');
        Route::post('/plans',[AdminController::class,'storePlan'])->name('plans.store');
        Route::get('/plans/{plan}/edit',[AdminController::class,'editPlan'])->name('plans.edit');
        Route::put('/plans/{plan}',[AdminController::class,'updatePlan'])->name('plans.update');
        Route::delete('/plans/{plan}',[AdminController::class,'deletePlan'])->name('plans.delete');
        Route::post('/plans/{plan}/toggle',[AdminController::class,'togglePlan'])->name('plans.toggle');
    });
});
Route::get('/admin/notifications/poll', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'poll'])->name('admin.notifications.poll');
