<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\NhaCungCap;

class NhaCungCapSeeder extends Seeder
{
    /**
     * Chạy Seeder để tạo dữ liệu mẫu Nhà cung cấp.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('nha_cung_cap')->delete();
        DB::statement('ALTER TABLE nha_cung_cap AUTO_INCREMENT = 1');
        Schema::enableForeignKeyConstraints();

        $nhaCungCaps = [
            [
                'ma_ncc'        => 'NCC001',
                'ten_ncc'       => 'Công ty Cổ phần Dược Hậu Giang (DHG)',
                'so_dien_thoai' => '02923891433',
                'email'         => 'dhgpharma@dhgpharma.com.vn',
                'dia_chi'       => '288 Bis Nguyễn Văn Cừ, Quận Ninh Kiều, TP. Cần Thơ',
                'ma_so_thue'    => '1800156801',
                'ghi_chu'       => 'Nhà cung cấp chính các loại thuốc giảm đau, kháng sinh',
                'trang_thai'    => 1,
            ],
            [
                'ma_ncc'        => 'NCC002',
                'ten_ncc'       => 'Công ty Cổ phần Traphaco',
                'so_dien_thoai' => '18006612',
                'email'         => 'info@traphaco.com.vn',
                'dia_chi'       => '75 Yên Ninh, Hoàng Mai, Hà Nội',
                'ma_so_thue'    => '0100108656',
                'ghi_chu'       => 'Chuyên cung cấp các sản phẩm đông dược, thực phẩm chức năng',
                'trang_thai'    => 1,
            ],
            [
                'ma_ncc'        => 'NCC003',
                'ten_ncc'       => 'Công ty Dược phẩm Eco (Eco Pharma)',
                'so_dien_thoai' => '02862936630',
                'email'         => 'eco@ecopharma.com.vn',
                'dia_chi'       => '148 Hoàng Hoa Thám, P.12, Q.Tân Bình, TP.HCM',
                'ma_so_thue'    => '0305452261',
                'ghi_chu'       => 'Phân phối Jex, Sâm Alipas...',
                'trang_thai'    => 1,
            ]
        ];

        foreach ($nhaCungCaps as $ncc) {
            NhaCungCap::create($ncc);
        }

        $this->command->info("Đã tạo 3 Nhà cung cấp mẫu thành công!");
    }
}