<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table("products", function (Blueprint $table) {
            $table->boolean("was_active_before_subscription_expiry")
                ->default(false)
                ->after("is_archived");
        });
    }

    public function down(): void
    {
        Schema::table("products", function (Blueprint $table) {
            $table->dropColumn("was_active_before_subscription_expiry");
        });
    }
};
