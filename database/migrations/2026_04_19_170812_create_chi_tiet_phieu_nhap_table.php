<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chi_tiet_phieu_nhap', function (Blueprint $table) {
            $table->id();
            
            // Liên kết với Phiếu Nhập (Xóa phiếu nhập thì tự động xóa chi tiết - cascade)
            $table->foreignId('phieu_nhap_id')->constrained('phieu_nhap')->cascadeOnDelete();
            
            // Liên kết với Thuốc (Không cho xóa Thuốc nếu nó đã từng được nhập kho)
            $table->foreignId('thuoc_id')->constrained('thuoc')->restrictOnDelete();
            
            // Dữ liệu lúc nhập
            $table->string('don_vi_nhap', 50)->comment('VD: Hộp, Thùng');
            $table->integer('so_luong_nhap')->comment('Số lượng theo Đơn vị nhập');
            $table->integer('gia_nhap')->comment('Giá nhập của 1 Đơn vị nhập (VD: Giá 1 Hộp)');
            
            // Dữ liệu quy đổi (Dùng để cộng vào kho)
            $table->integer('ty_le_quy_doi')->default(1)->comment('1 Đơn vị nhập = X Đơn vị cơ bản');
            $table->integer('so_luong_co_ban')->comment('Tự động tính: so_luong_nhap * ty_le_quy_doi');
            
            $table->integer('thanh_tien')->comment('Tự động tính: so_luong_nhap * gia_nhap');
            $table->date('han_su_dung_moi')->nullable()->comment('HSD của lô hàng này, dùng để update lại bảng thuốc');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_phieu_nhap');
    }
};