<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\KhachHang;

class KhachHangSeeder extends Seeder
{
    public function run(): void
    {
        // Tắt kiểm tra khóa ngoại để xóa dữ liệu cũ
        Schema::disableForeignKeyConstraints();
        DB::table('khach_hang')->delete();
        
        // ĐÃ SỬA: Xóa bỏ dòng ALTER TABLE AUTO_INCREMENT để tránh lỗi Syntax Error nếu bạn đang dùng SQLite
        
        Schema::enableForeignKeyConstraints();

        // Mật khẩu chung để test là: 123456
        $khachHangs = [
            [
                'ma_khach_hang'  => 'KH0001',
                'ten_khach_hang' => 'Trần Đức Duy',
                'so_dien_thoai'  => '0901234567',
                'password'       => Hash::make('123456'),
                'diem_tich_luy'  => 50000, 
                'ghi_chu'        => 'Khách VIP, tài khoản đã được kích hoạt',
                'trang_thai'     => 1, // Đã duyệt
            ],
            [
                'ma_khach_hang'  => 'KH0002',
                'ten_khach_hang' => 'Nguyễn Thị Bích',
                'so_dien_thoai'  => '0987654321',
                'password'       => Hash::make('123456'),
                'diem_tich_luy'  => 0,
                'ghi_chu'        => 'Tài khoản vừa đăng ký trên web, đang chờ Admin duyệt',
                'trang_thai'     => 0, // Chờ duyệt (test tính năng chặn đăng nhập)
            ]
        ];

        foreach ($khachHangs as $kh) {
            KhachHang::create($kh);
        }

        $this->command->info('Đã tạo dữ liệu Khách hàng mẫu kèm Mật khẩu (123456) thành công!');
    }
}