<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nhan_vien', function (Blueprint $table) {
            $table->id();
            $table->string('ma_nv', 20)->unique()->comment('Mã nhân viên, VD: NV001');
            $table->string('ho_ten')->comment('Họ và tên nhân viên');
            $table->string('email')->unique()->comment('Dùng để đăng nhập');
            $table->string('password')->comment('Mật khẩu đăng nhập');
            $table->string('so_dien_thoai', 15)->nullable();
            
            // Cột phân quyền: Quản trị viên hoặc Nhân viên quầy
            $table->enum('chuc_vu', ['admin', 'nhan_vien'])->default('nhan_vien');
            
            // Trạng thái: 1 là đang làm việc, 0 là đã nghỉ (khóa tài khoản)
            $table->boolean('trang_thai')->default(true);
            
            // Cột này bắt buộc phải có để dùng chức năng "Ghi nhớ đăng nhập" của Laravel
            $table->rememberToken(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nhan_vien');
    }
};
