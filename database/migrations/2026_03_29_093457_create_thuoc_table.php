<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chạy migration để tạo bảng thuốc với cấu trúc hoàn chỉnh.
     */
    public function up(): void
    {
        // Lưu ý: Nếu bạn đang dùng Schema::create, KHÔNG ĐƯỢC dùng ->after()
        Schema::create('thuoc', function (Blueprint $table) {
            $table->id(); // Khóa chính tự tăng
            
            // Thông tin cơ bản
            $table->string('ma_thuoc', 50)->unique()->comment('Mã vạch hoặc mã nội bộ');
            $table->string('ten_thuoc')->comment('Tên thương mại');
            
            // Thêm danh mục ngay sau tên thuốc (Thứ tự dòng code quyết định vị trí)
            $table->string('danh_muc')->nullable()->comment('Nhóm thuốc');
            $table->string('loai_thuoc')->default('OTC')->comment('Rx hoặc OTC');
            
            $table->string('hoat_chat')->nullable()->comment('Thành phần chính');
            
            // Cột đơn vị tính
            $table->string('don_vi_tinh', 20)->comment('Viên, Vỉ, Hộp, Chai, Tuýp...'); 
            
            // Giá và Tồn kho
            $table->integer('gia_ban')->default(0)->comment('Giá bán lẻ VNĐ');
            $table->integer('so_luong_ton')->default(0)->comment('Số lượng thực tế trong tủ');
            
            // Thêm vị trí ngay sau số lượng tồn
            $table->string('vi_tri')->nullable()->comment('Vị trí kệ hàng');
            
            // Các thông tin phụ
            $table->date('han_su_dung')->nullable()->comment('Hạn dùng của lô hiện tại');
            $table->string('hinh_anh')->nullable()->comment('Đường dẫn ảnh sản phẩm');
            
            // Trạng thái kinh doanh
            $table->boolean('trang_thai')->default(true)->comment('1: Đang bán, 0: Ngừng kinh doanh');
            
            $table->timestamps(); // Tự động tạo created_at và updated_at
        });
    }

    /**
     * Hoàn tác migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('thuoc');
    }
};