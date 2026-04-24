<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Models\PhieuNhap;
use App\Models\ChiTietPhieuNhap;
use App\Models\NhaCungCap;
use App\Models\NhanVien;
use App\Models\Thuoc;

class PhieuNhapSeeder extends Seeder
{
    /**
     * Tạo dữ liệu Phiếu nhập kho mẫu và các chi tiết thuốc đi kèm.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('chi_tiet_phieu_nhap')->delete();
        DB::statement('ALTER TABLE chi_tiet_phieu_nhap AUTO_INCREMENT = 1');
        DB::table('phieu_nhap')->delete();
        DB::statement('ALTER TABLE phieu_nhap AUTO_INCREMENT = 1');
        Schema::enableForeignKeyConstraints();

        $nhaCungCap = NhaCungCap::first();
        $nhanVien = NhanVien::first();
        $thuocs = Thuoc::take(5)->get();

        if (!$nhaCungCap || !$nhanVien || $thuocs->isEmpty()) {
            $this->command->error("Vui lòng chạy Seeder của Nhà cung cấp, Nhân viên và Thuốc trước!");
            return;
        }

        $this->command->info("Đang tạo Phiếu nhập kho mẫu...");

        // 1. Tạo Phiếu nhập gốc (Tạm để tổng tiền = 0)
        $phieuNhap = PhieuNhap::create([
            'ma_phieu'        => 'PN-' . Carbon::now()->format('Ymd') . '-01',
            'nha_cung_cap_id' => $nhaCungCap->id,
            'nhan_vien_id'    => $nhanVien->id,
            'tong_tien_nhap'  => 0,
            'trang_thai'      => 'completed', // Mô phỏng phiếu đã được duyệt
            'ghi_chu'         => 'Phiếu nhập hàng định kỳ mẫu'
        ]);

        $tongTienPhieu = 0;

        // 2. Tạo Chi tiết phiếu nhập cho từng loại thuốc
        foreach ($thuocs as $thuoc) {
            $soLuongNhap = rand(10, 50); // Nhập từ 10 - 50 Đơn vị nhập (VD: 10 Hộp)
            
            // Lấy tỷ lệ quy đổi. Nếu chưa có set là 1.
            $tyLe = $thuoc->ty_le_quy_doi > 0 ? $thuoc->ty_le_quy_doi : 1;
            
            // Giá nhập sỉ (1 Hộp) = Giá nhập cơ bản (1 Viên) * Tỷ lệ (100 Viên)
            // Nếu thuốc chưa có giá nhập, cho random từ 50k - 200k / đơn vị nhập
            $giaNhapSi = $thuoc->gia_nhap > 0 ? ($thuoc->gia_nhap * $tyLe) : rand(50, 200) * 1000;
            
            $thanhTien = $soLuongNhap * $giaNhapSi;
            $soLuongCoBan = $soLuongNhap * $tyLe; // Quy ra số lẻ để nhập kho
            $hsdMoi = Carbon::now()->addMonths(rand(12, 36));

            ChiTietPhieuNhap::create([
                'phieu_nhap_id'   => $phieuNhap->id,
                'thuoc_id'        => $thuoc->id,
                'don_vi_nhap'     => $thuoc->don_vi_nhap ?: 'Hộp',
                'so_luong_nhap'   => $soLuongNhap,
                'gia_nhap'        => $giaNhapSi,
                'ty_le_quy_doi'   => $tyLe,
                'so_luong_co_ban' => $soLuongCoBan,
                'thanh_tien'      => $thanhTien,
                'han_su_dung_moi' => $hsdMoi
            ]);

            $tongTienPhieu += $thanhTien;
        }

        // 3. Cập nhật lại tổng tiền của Phiếu Nhập
        $phieuNhap->update(['tong_tien_nhap' => $tongTienPhieu]);

        $this->command->info("Tạo thành công 1 Phiếu nhập kho với tổng tiền: " . number_format($tongTienPhieu) . " VNĐ");
    }
}