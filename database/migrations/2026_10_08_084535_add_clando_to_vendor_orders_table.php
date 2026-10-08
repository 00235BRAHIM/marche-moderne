<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendor_orders', function (Blueprint $table) {
            $table->string('clando_name', 150)
                ->nullable()
                ->after('payment_status');

            $table->string('clando_phone', 30)
                ->nullable()
                ->after('clando_name');
        });
    }

    public function down(): void
    {
        Schema::table('vendor_orders', function (Blueprint $table) {
            $table->dropColumn([
                'clando_name',
                'clando_phone',
            ]);
        });
    }
};
