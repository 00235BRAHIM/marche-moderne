<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::create('vendor_payment_methods', function(Blueprint $table){
  $table->id(); $table->foreignId('vendor_id')->constrained('users')->cascadeOnDelete();
  $table->enum('provider',['airtel_money','moov_money']); $table->string('account_name',150); $table->string('transfer_number',30);
  $table->boolean('is_active')->default(true); $table->timestamps(); $table->unique(['vendor_id','provider']);
 }); }
 public function down(): void { Schema::dropIfExists('vendor_payment_methods'); }
};
