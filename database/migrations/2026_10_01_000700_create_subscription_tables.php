<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->unsignedInteger('duration_days')->default(30);
            $table->unsignedInteger('product_limit')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('vendor_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('subscription_plan_id')->constrained('subscription_plans')->restrictOnDelete();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->enum('status', ['pending','active','expired','cancelled'])->default('pending');
            $table->enum('payment_status', ['unpaid','pending','paid','failed'])->default('unpaid');
            $table->string('payment_method')->nullable();
            $table->string('reference')->nullable()->unique();
            $table->text('admin_note')->nullable();
            $table->timestamps();
            $table->index(['vendor_id','status']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('vendor_subscriptions');
        Schema::dropIfExists('subscription_plans');
    }
};
