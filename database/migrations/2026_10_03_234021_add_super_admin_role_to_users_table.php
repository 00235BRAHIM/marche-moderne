<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('customer','vendor','admin','super_admin') NOT NULL DEFAULT 'customer'");
    }

    public function down(): void
    {
        DB::statement("UPDATE users SET role = 'admin' WHERE role = 'super_admin'");

        DB::statement("ALTER TABLE users MODIFY role ENUM('customer','vendor','admin') NOT NULL DEFAULT 'customer'");
    }
};
