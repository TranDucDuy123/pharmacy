<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KhachHang extends Model
{
    use HasFactory;

    protected $table = 'khach_hang';

    protected $fillable = [
        'ma_khach_hang',
        'ten_khach_hang',
        'so_dien_thoai',
        'diem_tich_luy',
        'ghi_chu',
        'trang_thai'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'khach_hang_id');
    }
}