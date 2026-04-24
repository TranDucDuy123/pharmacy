<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phieu_nhap', function (Blueprint $table) {
            $table->id();
            $table->string('ma_phieu', 50)->unique()->comment('VD: PN-20260420-01');
            
            // Khóa ngoại liên kết tới Nhà cung cấp (Bảo vệ dữ liệu, không cho xóa NCC nếu đã có phiếu nhập)
            $table->foreignId('nha_cung_cap_id')->constrained('nha_cung_cap')->restrictOnDelete();
            
            // Khóa ngoại liên kết tới Nhân viên lập phiếu (Bảng của bạn tên là nhan_vien)
            $table->foreignId('nhan_vien_id')->constrained('nhan_vien')->restrictOnDelete();
            
            $table->integer('tong_tien_nhap')->default(0);
            
            // Trạng thái phiếu: pending (nháp/chờ duyệt), completed (đã nhập kho), cancelled (đã hủy)
            $table->string('trang_thai', 20)->default('pending'); 
            
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phieu_nhap');
    }
};