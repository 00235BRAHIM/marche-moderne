<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('shop_name')->nullable()->after('name');
            $table->string('shop_slug')->nullable()->unique()->after('shop_name');
            $table->string('shop_logo')->nullable()->after('shop_slug');
            $table->text('shop_description')->nullable()->after('shop_logo');
            $table->string('facebook_url')->nullable()->after('shop_description');
            $table->string('youtube_url')->nullable()->after('facebook_url');
            $table->string('whatsapp_url')->nullable()->after('youtube_url');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['shop_slug']);

            $table->dropColumn([
                'shop_name',
                'shop_slug',
                'shop_logo',
                'shop_description',
                'facebook_url',
                'youtube_url',
                'whatsapp_url',
            ]);
        });
    }
};
