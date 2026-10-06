<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('vendor_status', ['pending','approved','suspended'])->default('pending')->after('role');
            $table->boolean('is_certified')->default(false)->after('vendor_status');
            $table->string('certification_number')->nullable()->unique()->after('is_certified');
            $table->string('certification_document')->nullable()->after('certification_number');
            $table->timestamp('certified_at')->nullable()->after('certification_document');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['vendor_status','is_certified','certification_number','certification_document','certified_at']);
        });
    }
};
