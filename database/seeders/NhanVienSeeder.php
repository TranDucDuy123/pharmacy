<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

// Duy CHÚ Ý thêm 2 dòng này vào nhé:
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class NhanVienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('nhan_vien')->insert([
            [
                'ma_nv' => 'ADMIN01',
                'ho_ten' => 'Quản trị viên Hệ thống',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('123456'), // Hash::make để mã hóa mật khẩu an toàn
                'so_dien_thoai' => '0901234567',
                'chuc_vu' => 'admin',
                'trang_thai' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_nv' => 'NV001',
                'ho_ten' => 'Nhân viên Quầy 1',
                'email' => 'nhanvien1@gmail.com',
                'password' => Hash::make('123456'),
                'so_dien_thoai' => '0987654321',
                'chuc_vu' => 'nhan_vien',
                'trang_thai' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
