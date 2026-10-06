<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('vendor_orders', function(Blueprint $t){
   $t->id(); $t->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
   $t->foreignId('vendor_id')->constrained('users')->cascadeOnDelete();
   $t->string('order_number')->unique(); $t->decimal('subtotal',15,2); $t->decimal('shipping_fee',15,2)->default(0); $t->decimal('total',15,2);
   $t->string('status')->default('pending'); $t->string('payment_status')->default('pending'); $t->timestamps();
   $t->unique(['order_id','vendor_id']);
  });
  Schema::table('order_items', function(Blueprint $t){ $t->foreignId('vendor_order_id')->nullable()->after('order_id')->constrained('vendor_orders')->nullOnDelete(); });
  Schema::table('manual_payment_submissions', function(Blueprint $t){ $t->foreignId('vendor_order_id')->nullable()->after('order_id')->constrained('vendor_orders')->nullOnDelete(); });
 }
 public function down(): void {
  Schema::table('manual_payment_submissions', fn(Blueprint $t)=>$t->dropConstrainedForeignId('vendor_order_id'));
  Schema::table('order_items', fn(Blueprint $t)=>$t->dropConstrainedForeignId('vendor_order_id'));
  Schema::dropIfExists('vendor_orders');
 }
};
