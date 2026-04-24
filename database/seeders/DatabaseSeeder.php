<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
             // 0. Quản trị viên và Nhân viên (Cần tạo trước tiên để có người thao tác)
            NhanVienSeeder::class,

            // 1. Dữ liệu nền tảng (Độc lập, không phụ thuộc ai)
            KhachHangSeeder::class,
            NhaCungCapSeeder::class,

            // 2. Bắt buộc phải chạy ThuocSeeder trước để có sản phẩm trong kho
            ThuocSeeder::class,
            
            // 3. Tạo phiếu nhập kho (Phụ thuộc vào Nhà cung cấp, Nhân viên và Thuốc)
            PhieuNhapSeeder::class,
            
            // 4. Tạo khung Hóa đơn bán hàng trống (Phụ thuộc vào Khách Hàng và Nhân Viên)
            OrderSeeder::class,

            // 5. QUAN TRỌNG: Thêm chi tiết thuốc vào hóa đơn (Phụ thuộc vào Hóa đơn và Thuốc)
            OrderItemSeeder::class,
        ]);
    }
}
