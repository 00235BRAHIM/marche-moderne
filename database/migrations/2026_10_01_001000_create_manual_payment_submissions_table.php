<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::create('manual_payment_submissions', function(Blueprint $table){
  $table->id(); $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
  $table->foreignId('vendor_id')->nullable()->constrained('users')->nullOnDelete();
  $table->foreignId('vendor_subscription_id')->nullable()->constrained('vendor_subscriptions')->nullOnDelete();
  $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
  $table->enum('provider',['airtel_money','moov_money']); $table->string('transfer_number',30); $table->string('payer_name',150);
  $table->string('transaction_reference')->nullable(); $table->decimal('amount',15,2); $table->string('currency',10)->default('XAF');
  $table->string('screenshot_path'); $table->enum('status',['pending','approved','rejected'])->default('pending');
  $table->text('admin_note')->nullable(); $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete(); $table->timestamp('validated_at')->nullable(); $table->timestamps();
 }); }
 public function down(): void { Schema::dropIfExists('manual_payment_submissions'); }
};
