<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ThuocSeeder extends Seeder
{
    public function run(): void
    {
        // Tắt kiểm tra khóa ngoại
        Schema::disableForeignKeyConstraints();
        
        // Dùng delete() thay vì truncate() để tránh lỗi MariaDB
        DB::table('thuoc')->delete();
        // Reset lại ID tự tăng (Auto Increment) về 1
        DB::statement('ALTER TABLE thuoc AUTO_INCREMENT = 1');
        
        // Bật lại kiểm tra khóa ngoại
        Schema::enableForeignKeyConstraints();

        $now = Carbon::now();
        $thuocThucTe = [
            // 1. Giảm đau, hạ sốt
            ['ten' => 'Panadol Extra', 'hc' => 'Paracetamol, Caffeine', 'dvt' => 'Vỉ', 'gia' => 15000, 'dm' => 'Giảm đau, hạ sốt', 'loai' => 'OTC', 'vt' => 'Kệ A1'],
            ['ten' => 'Hapacol 500', 'hc' => 'Paracetamol 500mg', 'dvt' => 'Vỉ', 'gia' => 12000, 'dm' => 'Giảm đau, hạ sốt', 'loai' => 'OTC', 'vt' => 'Kệ A1'],
            ['ten' => 'Efferalgan 500mg', 'hc' => 'Paracetamol', 'dvt' => 'Viên sủi', 'gia' => 4000, 'dm' => 'Giảm đau, hạ sốt', 'loai' => 'OTC', 'vt' => 'Kệ A1'],
            ['ten' => 'Alaxan', 'hc' => 'Paracetamol, Ibuprofen', 'dvt' => 'Vỉ', 'gia' => 12000, 'dm' => 'Giảm đau, hạ sốt', 'loai' => 'OTC', 'vt' => 'Kệ A1'],
            ['ten' => 'Tiffy Dey', 'hc' => 'Paracetamol, CPM...', 'dvt' => 'Vỉ', 'gia' => 15000, 'dm' => 'Giảm đau, hạ sốt', 'loai' => 'OTC', 'vt' => 'Kệ A2'],
            ['ten' => 'Ibuprofen 400mg', 'hc' => 'Ibuprofen', 'dvt' => 'Vỉ', 'gia' => 10000, 'dm' => 'Giảm đau, hạ sốt', 'loai' => 'OTC', 'vt' => 'Kệ A1'],
            ['ten' => 'Decolgen Forte', 'hc' => 'Paracetamol, CPM', 'dvt' => 'Vỉ', 'gia' => 10000, 'dm' => 'Giảm đau, hạ sốt', 'loai' => 'OTC', 'vt' => 'Kệ A2'],

            // 2. Kháng sinh & Chống viêm
            ['ten' => 'Augmentin 1g', 'hc' => 'Amoxicillin, Clavulanic acid', 'dvt' => 'Viên', 'gia' => 18000, 'dm' => 'Kháng sinh', 'loai' => 'Rx', 'vt' => 'Kệ B1'],
            ['ten' => 'Amoxicillin 500mg', 'hc' => 'Amoxicillin', 'dvt' => 'Vỉ', 'gia' => 12000, 'dm' => 'Kháng sinh', 'loai' => 'Rx', 'vt' => 'Kệ B1'],
            ['ten' => 'Cefuroxim 500mg', 'hc' => 'Cefuroxim', 'dvt' => 'Vỉ', 'gia' => 25000, 'dm' => 'Kháng sinh', 'loai' => 'Rx', 'vt' => 'Kệ B2'],
            ['ten' => 'Azithromycin 250mg', 'hc' => 'Azithromycin', 'dvt' => 'Vỉ', 'gia' => 30000, 'dm' => 'Kháng sinh', 'loai' => 'Rx', 'vt' => 'Kệ B2'],
            ['ten' => 'Cefixim 200mg', 'hc' => 'Cefixim', 'dvt' => 'Vỉ', 'gia' => 22000, 'dm' => 'Kháng sinh', 'loai' => 'Rx', 'vt' => 'Kệ B2'],
            ['ten' => 'Alpha Choay', 'hc' => 'Chymotrypsin', 'dvt' => 'Vỉ', 'gia' => 22000, 'dm' => 'Chống viêm', 'loai' => 'Rx', 'vt' => 'Kệ B3'],
            ['ten' => 'Medrol 16mg', 'hc' => 'Methylprednisolon', 'dvt' => 'Vỉ', 'gia' => 40000, 'dm' => 'Chống viêm', 'loai' => 'Rx', 'vt' => 'Kệ B3'],

            // 3. Tiêu hóa & Dạ dày
            ['ten' => 'Berberin 10mg', 'hc' => 'Berberin clorid', 'dvt' => 'Lọ', 'gia' => 20000, 'dm' => 'Tiêu hóa', 'loai' => 'OTC', 'vt' => 'Kệ C1'],
            ['ten' => 'Enterogermina', 'hc' => 'Bào tử Bacillus clausii', 'dvt' => 'Ống', 'gia' => 8000, 'dm' => 'Tiêu hóa', 'loai' => 'OTC', 'vt' => 'Kệ C2'],
            ['ten' => 'Smecta', 'hc' => 'Diosmectit', 'dvt' => 'Gói', 'gia' => 4000, 'dm' => 'Tiêu hóa', 'loai' => 'OTC', 'vt' => 'Kệ C1'],
            ['ten' => 'Oresol', 'hc' => 'Glucose, Natri clorid...', 'dvt' => 'Gói', 'gia' => 3000, 'dm' => 'Tiêu hóa', 'loai' => 'OTC', 'vt' => 'Kệ C1'],
            ['ten' => 'Omeprazol 20mg', 'hc' => 'Omeprazol', 'dvt' => 'Vỉ', 'gia' => 15000, 'dm' => 'Dạ dày', 'loai' => 'Rx', 'vt' => 'Kệ C3'],
            ['ten' => 'Pantoprazol 40mg', 'hc' => 'Pantoprazol', 'dvt' => 'Vỉ', 'gia' => 20000, 'dm' => 'Dạ dày', 'loai' => 'Rx', 'vt' => 'Kệ C3'],
            ['ten' => 'Phosphalugel', 'hc' => 'Nhôm phosphat', 'dvt' => 'Gói', 'gia' => 5000, 'dm' => 'Dạ dày', 'loai' => 'OTC', 'vt' => 'Kệ C3'],
            ['ten' => 'Fugacar', 'hc' => 'Mebendazol', 'dvt' => 'Viên', 'gia' => 20000, 'dm' => 'Tẩy giun', 'loai' => 'OTC', 'vt' => 'Kệ C4'],

            // 4. Hệ hô hấp
            ['ten' => 'Loratadin 10mg', 'hc' => 'Loratadin', 'dvt' => 'Vỉ', 'gia' => 10000, 'dm' => 'Hệ hô hấp', 'loai' => 'OTC', 'vt' => 'Kệ D1'],
            ['ten' => 'Acetylcystein 200mg', 'hc' => 'Acetylcystein', 'dvt' => 'Gói', 'gia' => 2000, 'dm' => 'Hệ hô hấp', 'loai' => 'OTC', 'vt' => 'Kệ D1'],
            ['ten' => 'Bisolvon', 'hc' => 'Bromhexin', 'dvt' => 'Vỉ', 'gia' => 18000, 'dm' => 'Hệ hô hấp', 'loai' => 'OTC', 'vt' => 'Kệ D1'],
            ['ten' => 'Prospan', 'hc' => 'Cao khô lá thường xuân', 'dvt' => 'Chai', 'gia' => 75000, 'dm' => 'Hệ hô hấp', 'loai' => 'OTC', 'vt' => 'Kệ D2'],
            ['ten' => 'Otrivin 0.1%', 'hc' => 'Xylometazolin', 'dvt' => 'Lọ', 'gia' => 45000, 'dm' => 'Hệ hô hấp', 'loai' => 'OTC', 'vt' => 'Kệ D2'],

            // 5. Vitamin & Khoáng chất
            ['ten' => 'Vitamin C 500mg', 'hc' => 'Acid Ascorbic', 'dvt' => 'Lọ', 'gia' => 35000, 'dm' => 'Vitamin', 'loai' => 'OTC', 'vt' => 'Kệ E1'],
            ['ten' => 'Magne B6', 'hc' => 'Magnesi lactat, Vitamin B6', 'dvt' => 'Vỉ', 'gia' => 15000, 'dm' => 'Vitamin', 'loai' => 'OTC', 'vt' => 'Kệ E1'],
            ['ten' => 'Canxi Corbiere', 'hc' => 'Canxi glucoheptonat', 'dvt' => 'Ống', 'gia' => 12000, 'dm' => 'Vitamin', 'loai' => 'OTC', 'vt' => 'Kệ E1'],
            ['ten' => 'Sắt Ferrovit', 'hc' => 'Sắt fumarat, Acid Folic', 'dvt' => 'Vỉ', 'gia' => 18000, 'dm' => 'Vitamin', 'loai' => 'OTC', 'vt' => 'Kệ E2'],
            ['ten' => 'Enat 400', 'hc' => 'Vitamin E', 'dvt' => 'Vỉ', 'gia' => 35000, 'dm' => 'Vitamin', 'loai' => 'OTC', 'vt' => 'Kệ E2'],
            ['ten' => 'Vitamin 3B', 'hc' => 'Vitamin B1, B6, B12', 'dvt' => 'Vỉ', 'gia' => 10000, 'dm' => 'Vitamin', 'loai' => 'OTC', 'vt' => 'Kệ E2'],

            // 6. Tim mạch & Huyết áp
            ['ten' => 'Amlodipin 5mg', 'hc' => 'Amlodipin', 'dvt' => 'Vỉ', 'gia' => 8000, 'dm' => 'Tim mạch', 'loai' => 'Rx', 'vt' => 'Kệ F1'],
            ['ten' => 'Losartan 50mg', 'hc' => 'Losartan', 'dvt' => 'Vỉ', 'gia' => 15000, 'dm' => 'Tim mạch', 'loai' => 'Rx', 'vt' => 'Kệ F1'],
            ['ten' => 'Concor 5mg', 'hc' => 'Bisoprolol', 'dvt' => 'Vỉ', 'gia' => 45000, 'dm' => 'Tim mạch', 'loai' => 'Rx', 'vt' => 'Kệ F1'],
            ['ten' => 'Atorvastatin 20mg', 'hc' => 'Atorvastatin', 'dvt' => 'Vỉ', 'gia' => 20000, 'dm' => 'Tim mạch', 'loai' => 'Rx', 'vt' => 'Kệ F2'],
            ['ten' => 'Rosuvastatin 10mg', 'hc' => 'Rosuvastatin', 'dvt' => 'Vỉ', 'gia' => 30000, 'dm' => 'Tim mạch', 'loai' => 'Rx', 'vt' => 'Kệ F2'],

            // 7. Tiểu đường
            ['ten' => 'Metformin 500mg', 'hc' => 'Metformin', 'dvt' => 'Vỉ', 'gia' => 10000, 'dm' => 'Tiểu đường', 'loai' => 'Rx', 'vt' => 'Kệ G1'],
            ['ten' => 'Glimepirid 2mg', 'hc' => 'Glimepirid', 'dvt' => 'Vỉ', 'gia' => 12000, 'dm' => 'Tiểu đường', 'loai' => 'Rx', 'vt' => 'Kệ G1'],
            ['ten' => 'Diamicron MR 30mg', 'hc' => 'Gliclazid', 'dvt' => 'Vỉ', 'gia' => 40000, 'dm' => 'Tiểu đường', 'loai' => 'Rx', 'vt' => 'Kệ G1'],

            // 8. Thuốc bôi ngoài da
            ['ten' => 'Salonpas', 'hc' => 'Methyl Salicylate, L-Menthol', 'dvt' => 'Hộp', 'gia' => 15000, 'dm' => 'Giảm đau ngoài da', 'loai' => 'OTC', 'vt' => 'Kệ A3'],
            ['ten' => 'Voltaren Emulgel', 'hc' => 'Diclofenac', 'dvt' => 'Tuýp', 'gia' => 65000, 'dm' => 'Giảm đau ngoài da', 'loai' => 'OTC', 'vt' => 'Kệ A3'],

            // 9. Vật tư y tế & Khác
            ['ten' => 'Nước muối 0.9%', 'hc' => 'Natri Clorid 0.9%', 'dvt' => 'Chai', 'gia' => 6000, 'dm' => 'Vật tư y tế', 'loai' => 'OTC', 'vt' => 'Kệ H1'],
            ['ten' => 'Cồn 70 độ', 'hc' => 'Ethanol 70%', 'dvt' => 'Chai', 'gia' => 10000, 'dm' => 'Vật tư y tế', 'loai' => 'OTC', 'vt' => 'Kệ H1'],
            ['ten' => 'Povidine 10%', 'hc' => 'Povidon iod', 'dvt' => 'Lọ', 'gia' => 15000, 'dm' => 'Vật tư y tế', 'loai' => 'OTC', 'vt' => 'Kệ H1'],
            ['ten' => 'Băng cá nhân Urgo', 'hc' => 'Băng gạc vô trùng', 'dvt' => 'Hộp', 'gia' => 35000, 'dm' => 'Vật tư y tế', 'loai' => 'OTC', 'vt' => 'Kệ H1'],
            ['ten' => 'Bông y tế Bạch Tuyết', 'hc' => 'Bông gòn 100% cotton', 'dvt' => 'Cuộn', 'gia' => 25000, 'dm' => 'Vật tư y tế', 'loai' => 'OTC', 'vt' => 'Kệ H1'],
            ['ten' => 'Khẩu trang y tế 4 lớp', 'hc' => 'Vải không dệt', 'dvt' => 'Hộp', 'gia' => 40000, 'dm' => 'Vật tư y tế', 'loai' => 'OTC', 'vt' => 'Kệ H2'],
            ['ten' => 'Que thử thai Quickstick', 'hc' => 'Kháng thể hCG', 'dvt' => 'Hộp', 'gia' => 20000, 'dm' => 'Vật tư y tế', 'loai' => 'OTC', 'vt' => 'Kệ H2'],
            ['ten' => 'Gạc y tế tiệt trùng', 'hc' => 'Gạc cotton', 'dvt' => 'Gói', 'gia' => 5000, 'dm' => 'Vật tư y tế', 'loai' => 'OTC', 'vt' => 'Kệ H1'],
        ];

        foreach ($thuocThucTe as $index => $item) {
            $dvtCoBan = $item['dvt'];
            $dvtNhap  = 'Hộp';
            $tyLe     = 1;

            // Thuật toán gán tự động Đơn vị nhập và Tỷ lệ dựa vào Đơn vị cơ bản
            if (in_array($dvtCoBan, ['Vỉ', 'Gói', 'Ống'])) {
                $dvtNhap = 'Hộp';
                $tyLe    = 10; // Giả sử 1 Hộp = 10 Vỉ
            } elseif ($dvtCoBan === 'Viên' || $dvtCoBan === 'Viên sủi') {
                $dvtNhap = 'Hộp';
                $tyLe    = 100; // Giả sử 1 Hộp = 100 Viên
            } elseif (in_array($dvtCoBan, ['Lọ', 'Chai', 'Tuýp', 'Cuộn'])) {
                $dvtNhap = 'Thùng';
                $tyLe    = 50; // Giả sử 1 Thùng = 50 Lọ
            } elseif ($dvtCoBan === 'Hộp') {
                $dvtNhap = 'Thùng';
                $tyLe    = 24; // Giả sử 1 Thùng = 24 Hộp
            }

            // Tính giá nhập bằng 70% giá bán (lợi nhuận 30%)
            $giaNhap = intval($item['gia'] * 0.7);

            DB::table('thuoc')->insert([
                'ma_thuoc'      => 'SP' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'ten_thuoc'     => $item['ten'],
                'hoat_chat'     => $item['hc'],
                'don_vi_tinh'   => $dvtCoBan, // Giữ lại cột cũ để tương thích ngược nếu cần
                'don_vi_co_ban' => $dvtCoBan, // Cột mới
                'don_vi_nhap'   => $dvtNhap,  // Cột mới
                'ty_le_quy_doi' => $tyLe,     // Cột mới
                'gia_nhap'      => $giaNhap,  // Cột mới
                'gia_ban'       => $item['gia'],
                'danh_muc'      => $item['dm'],
                'loai_thuoc'    => $item['loai'],
                'vi_tri'        => $item['vt'],
                'so_luong_ton'  => rand(50, 500), // Random kho tồn dồi dào hơn một chút
                'han_su_dung'   => $now->copy()->addDays(rand(365, 1000)),
                'trang_thai'    => 1,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }
        
        $this->command->info('Đã seed thành công ' . count($thuocThucTe) . ' loại thuốc kèm cấu trúc quy đổi mới!');
    }
}