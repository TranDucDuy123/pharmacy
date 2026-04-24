<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiTietPhieuNhap extends Model
{
    use HasFactory;

    protected $table = 'chi_tiet_phieu_nhap';

    protected $fillable = [
        'phieu_nhap_id',
        'thuoc_id',
        
        // Thông tin nhập (Sỉ)
        'don_vi_nhap',     // VD: Hộp, Thùng
        'so_luong_nhap',   // VD: 5
        'gia_nhap',        // Giá nhập 1 Đơn vị SỈ (1 Hộp)
        
        // Thông tin quy đổi (Lẻ - dùng để cộng vào tồn kho)
        'ty_le_quy_doi',   // VD: 1 Hộp = 100 Viên
        'so_luong_co_ban', // Tự động tính = so_luong_nhap * ty_le_quy_doi (VD: 500)
        
        'thanh_tien',      // Tự động tính = so_luong_nhap * gia_nhap
        'han_su_dung_moi'  // Hạn sử dụng của lô hàng này (Để update sang bảng thuốc)
    ];

    protected $casts = [
        'so_luong_nhap'   => 'integer',
        'gia_nhap'        => 'integer',
        'ty_le_quy_doi'   => 'integer',
        'so_luong_co_ban' => 'integer',
        'thanh_tien'      => 'integer',
        'han_su_dung_moi' => 'date',
    ];

    public function phieuNhap(): BelongsTo
    {
        return $this->belongsTo(PhieuNhap::class, 'phieu_nhap_id', 'id');
    }

    public function thuoc(): BelongsTo
    {
        return $this->belongsTo(Thuoc::class, 'thuoc_id', 'id');
    }
}