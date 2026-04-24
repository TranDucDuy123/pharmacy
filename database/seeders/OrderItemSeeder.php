<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Thuoc;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// ĐÃ SỬA: Bỏ chữ "s" ở cuối, đổi từ OrderItemSeeders thành OrderItemSeeder
class OrderItemSeeder extends Seeder
{
    /**
     * Chạy Seeder để thêm thuốc vào các Hóa đơn đã tạo và tính lại tổng tiền.
     */
    public function run(): void
    {
        // 1. Tắt kiểm tra khóa ngoại và làm sạch bảng order_items
        Schema::disableForeignKeyConstraints();
        DB::table('order_items')->delete();
        DB::statement('ALTER TABLE order_items AUTO_INCREMENT = 1');
        Schema::enableForeignKeyConstraints();

        $orders = Order::all();
        // Chỉ lấy những thuốc đang ở trạng thái kinh doanh
        $dsThuoc = Thuoc::where('trang_thai', 1)->get();

        // Nếu chưa có hóa đơn hoặc chưa có thuốc thì dừng lại
        if ($dsThuoc->isEmpty() || $orders->isEmpty()) {
            $this->command->error("Thiếu dữ liệu Hóa đơn hoặc Thuốc. Vui lòng chạy OrderSeeder và ThuocSeeder trước.");
            return;
        }

        $this->command->info("Đang thêm chi tiết thuốc vào các hóa đơn...");

        // 2. Duyệt qua từng hóa đơn để bốc thuốc ngẫu nhiên bỏ vào
        foreach ($orders as $order) {
            $total = 0;
            // Chọn ngẫu nhiên từ 1 đến 5 loại thuốc khác nhau cho mỗi hóa đơn
            $soLoaiThuoc = rand(1, 5); 
            $items = $dsThuoc->random(min($soLoaiThuoc, $dsThuoc->count()));

            foreach ($items as $thuoc) {
                $qty = rand(1, 4); // Mua ngẫu nhiên từ 1 đến 4 (hộp/vỉ/chai...)
                $price = $thuoc->gia_ban; // Lấy giá bán gốc của thuốc

                OrderItem::create([
                    'order_id'   => $order->id,
                    'thuoc_id'   => $thuoc->id,
                    'quantity'   => $qty,
                    'price'      => $price,
                    // Đồng bộ thời gian tạo chi tiết khớp với thời gian tạo hóa đơn gốc
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                ]);

                // Cộng dồn thành tiền
                $total += ($qty * $price);
            }

            // 3. Cập nhật lại tổng tiền chính xác cho Hóa đơn gốc
            $order->update(['total_price' => $total]);
        }

        $this->command->info("Hoàn tất thêm chi tiết và cập nhật tổng tiền thành công!");
    }
}