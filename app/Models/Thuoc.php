<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Thuoc extends Model
{
    use HasFactory;

    protected $table = 'thuoc';

    protected $fillable = [
        'ma_thuoc', 
        'ten_thuoc', 
        'hoat_chat', 
        'danh_muc', 
        'loai_thuoc', 
        'don_vi_tinh',   // THÊM DÒNG NÀY: Để tương thích ngược với Database cũ, tránh lỗi NOT NULL
        'don_vi_co_ban', // Đơn vị nhỏ nhất (Viên, Gói...)
        'don_vi_nhap',   // Đơn vị khi nhập hàng (Hộp, Thùng...)
        'ty_le_quy_doi', // 1 Đơn vị nhập = bao nhiêu Đơn vị cơ bản
        'gia_nhap',      // Giá nhập tính trên Đơn vị cơ bản
        'gia_ban',       // Giá bán tính trên Đơn vị cơ bản
        'so_luong_ton',  // Luôn lưu theo Đơn vị cơ bản
        'han_su_dung', 
        'hinh_anh', 
        'vi_tri', 
        'trang_thai'
    ];

    protected $casts = [
        'gia_nhap'      => 'integer',
        'gia_ban'       => 'integer',
        'so_luong_ton'  => 'integer',
        'ty_le_quy_doi' => 'integer',
        'trang_thai'    => 'boolean',
        'han_su_dung'   => 'date',
    ];

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'thuoc_id', 'id');
    }

    /**
     * Helper: Hiển thị tồn kho dưới dạng quy đổi (VD: 5 Hộp 20 Viên)
     */
    public function getTonKhoHienThiAttribute()
    {
        if ($this->ty_le_quy_doi <= 1 || empty($this->don_vi_nhap)) {
            return "{$this->so_luong_ton} {$this->don_vi_co_ban}";
        }

        $soHop = floor($this->so_luong_ton / $this->ty_le_quy_doi);
        $soVienLe = $this->so_luong_ton % $this->ty_le_quy_doi;

        $ketQua = [];
        if ($soHop > 0) $ketQua[] = "{$soHop} {$this->don_vi_nhap}";
        if ($soVienLe > 0) $ketQua[] = "{$soVienLe} {$this->don_vi_co_ban}";

        return count($ketQua) > 0 ? implode(' ', $ketQua) : "Hết hàng";
    }

    public function scopeDangKinhDoanh($query)
    {
        return $query->where('trang_thai', 1);
    }

    public function scopeConHang($query)
    {
        return $query->where('so_luong_ton', '>', 0);
    }
}