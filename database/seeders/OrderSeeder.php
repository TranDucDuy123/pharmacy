<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\NhanVien;
use App\Models\KhachHang; // Gọi thêm Model Khách Hàng
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrderSeeder extends Seeder
{
    /**
     * Chạy Seeder để tạo danh sách Hóa đơn trống (chưa có thuốc).
     */
    public function run(): void
    {
        // 1. Tắt kiểm tra khóa ngoại và làm sạch bảng orders
        Schema::disableForeignKeyConstraints();
        DB::table('orders')->delete();
        DB::statement('ALTER TABLE orders AUTO_INCREMENT = 1');
        Schema::enableForeignKeyConstraints();

        // 2. Lấy Nhân viên đầu tiên để làm người bán hàng
        $nhanVien = NhanVien::first();
        
        if (!$nhanVien) {
            $nhanVien = NhanVien::create([
                'ma_nv'         => 'NV001',
                'ho_ten'        => 'Dược sĩ mẫu', 
                'email'         => 'duocsi@gmail.com',
                'password'      => bcrypt('123456'),
                'so_dien_thoai' => '0987654321',
                'chuc_vu'       => 'Nhân viên bán hàng',
                'trang_thai'    => 1
            ]);
        }

        // Lấy danh sách Khách hàng hiện có trong hệ thống
        $khachHangs = KhachHang::all();

        $this->command->info("Đang tạo 50 hóa đơn rỗng (có mix random Khách hàng)...");

        $orders = [];
        // 3. Khởi tạo mảng 50 hóa đơn ngẫu nhiên trong 60 ngày qua
        for ($i = 1; $i <= 50; $i++) {
            $date = Carbon::now()->subDays(rand(0, 60))->subMinutes(rand(0, 1440));
            
            // Random khách hàng: 70% hóa đơn có định danh khách hàng, 30% khách vãng lai
            $khachHangId = null;
            if ($khachHangs->isNotEmpty() && rand(1, 100) <= 70) {
                $khachHangId = $khachHangs->random()->id;
            }
            
            $orders[] = [
                'user_id'       => $nhanVien->id, 
                'customer_id'   => $khachHangId, // Đã trả lại thành customer_id cho khớp với DB của bạn
                'total_price'   => 0, 
                'status'        => 'completed',
                'note'          => 'Giao dịch POS mẫu #' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'created_at'    => $date,
                'updated_at'    => $date,
            ];
        }

        // 4. Sử dụng insert() để nạp tất cả dữ liệu vào Database cùng 1 lúc
        Order::insert($orders);

        $this->command->info("Đã tạo 50 hóa đơn rỗng thành công!");
    }
}