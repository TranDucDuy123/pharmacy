<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chạy migration (Thêm các cột phục vụ Phase 2 vào bảng thuoc).
     */
    public function up(): void
    {
        Schema::table('thuoc', function (Blueprint $table) {
            // Thêm các cột quy đổi đơn vị và nhập kho nếu chưa tồn tại
            if (!Schema::hasColumn('thuoc', 'don_vi_co_ban')) {
                $table->string('don_vi_co_ban', 50)->nullable()->after('don_vi_tinh')->comment('Đơn vị bán lẻ nhỏ nhất (Viên, Gói...)');
            }
            
            if (!Schema::hasColumn('thuoc', 'don_vi_nhap')) {
                $table->string('don_vi_nhap', 50)->nullable()->after('don_vi_co_ban')->comment('Đơn vị khi nhập hàng (Hộp, Thùng...)');
            }
            
            if (!Schema::hasColumn('thuoc', 'ty_le_quy_doi')) {
                $table->integer('ty_le_quy_doi')->default(1)->after('don_vi_nhap')->comment('1 Đơn vị nhập = X Đơn vị cơ bản');
            }
            
            if (!Schema::hasColumn('thuoc', 'gia_nhap')) {
                $table->integer('gia_nhap')->nullable()->after('ty_le_quy_doi')->comment('Giá nhập tính trên 1 Đơn vị cơ bản');
            }
        });
    }

    /**
     * Hoàn tác migration.
     */
    public function down(): void
    {
        Schema::table('thuoc', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('thuoc', 'don_vi_co_ban')) $columnsToDrop[] = 'don_vi_co_ban';
            if (Schema::hasColumn('thuoc', 'don_vi_nhap')) $columnsToDrop[] = 'don_vi_nhap';
            if (Schema::hasColumn('thuoc', 'ty_le_quy_doi')) $columnsToDrop[] = 'ty_le_quy_doi';
            if (Schema::hasColumn('thuoc', 'gia_nhap')) $columnsToDrop[] = 'gia_nhap';

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};