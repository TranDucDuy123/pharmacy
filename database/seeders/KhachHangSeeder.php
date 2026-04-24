<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KhachHang;

class KhachHangSeeder extends Seeder
{
    public function run(): void
    {
        $khachHangs = [
            [
                'ma_khach_hang'  => 'KH0001',
                'ten_khach_hang' => 'Trần Đức Duy',
                'so_dien_thoai'  => '0901234567',
                'diem_tich_luy'  => 50000, // Khách VIP đã có sẵn 50k điểm
                'ghi_chu'        => 'Khách quen, hay mua thuốc dạ dày'
            ],
            [
                'ma_khach_hang'  => 'KH0002',
                'ten_khach_hang' => 'Nguyễn Thị Bích',
                'so_dien_thoai'  => '0987654321',
                'diem_tich_luy'  => 0,
                'ghi_chu'        => ''
            ]
        ];

        foreach ($khachHangs as $kh) {
            KhachHang::create($kh);
        }
    }
}