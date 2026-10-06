<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_subscription_id')->constrained('vendor_subscriptions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('provider');
            $table->string('external_id')->nullable()->index();
            $table->string('reference')->unique();
            $table->string('phone', 30);
            $table->decimal('amount', 12, 2);
            $table->string('currency', 8)->default('XAF');
            $table->enum('status', ['initiated','pending','paid','failed','cancelled'])->default('initiated');
            $table->text('failure_reason')->nullable();
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->index(['provider','status']);
        });
    }
    public function down(): void { Schema::dropIfExists('payment_transactions'); }
};
