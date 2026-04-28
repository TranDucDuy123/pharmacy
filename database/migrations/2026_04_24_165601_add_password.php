<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('khach_hang', function (Blueprint $table) {
            // Thêm nếu chưa tồn tại
            if (!Schema::hasColumn('khach_hang', 'password')) {
                $table->string('password')->nullable()->after('so_dien_thoai');
            }

            if (!Schema::hasColumn('khach_hang', 'remember_token')) {
                $table->rememberToken();
            }
        });
    }

    public function down(): void
    {
        Schema::table('khach_hang', function (Blueprint $table) {
            if (Schema::hasColumn('khach_hang', 'password')) {
                $table->dropColumn('password');
            }

            if (Schema::hasColumn('khach_hang', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
        });
    }
};