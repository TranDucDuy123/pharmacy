<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NhaCungCap extends Model
{
    use HasFactory;

    protected $table = 'nha_cung_cap';

    protected $fillable = [
        'ma_ncc',
        'ten_ncc',
        'so_dien_thoai',
        'email',
        'dia_chi',
        'ma_so_thue',
        'ghi_chu',
        'trang_thai'
    ];

    protected $casts = [
        'trang_thai' => 'boolean',
    ];

    /**
     * Quan hệ 1-N: 1 Nhà cung cấp có nhiều Phiếu nhập
     */
    public function phieuNhaps(): HasMany
    {
        return $this->hasMany(PhieuNhap::class, 'nha_cung_cap_id', 'id');
    }

    /**
     * Scope: Lấy các nhà cung cấp đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('trang_thai', 1);
    }
}