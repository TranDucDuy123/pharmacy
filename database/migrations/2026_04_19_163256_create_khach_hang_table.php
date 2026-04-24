<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('khach_hang', function (Blueprint $table) {
            $table->id();
            $table->string('ma_khach_hang')->unique()->comment('Mã tự động, VD: KH0001');
            $table->string('ten_khach_hang');
            $table->string('so_dien_thoai', 20)->unique()->comment('Dùng để tìm kiếm nhanh ở POS');
            $table->integer('diem_tich_luy')->default(0)->comment('1 điểm = 1 VNĐ hoặc theo quy đổi');
            $table->text('ghi_chu')->nullable();
            $table->boolean('trang_thai')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('khach_hang');
    }
};