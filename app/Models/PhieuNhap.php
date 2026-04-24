<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PhieuNhap extends Model
{
    use HasFactory;

    protected $table = 'phieu_nhap';

    protected $fillable = [
        'ma_phieu',
        'nha_cung_cap_id',
        'nhan_vien_id',
        'tong_tien_nhap',
        'trang_thai', // VD: 'pending' (nháp), 'completed' (đã nhập kho), 'cancelled' (hủy)
        'ghi_chu'
    ];

    protected $casts = [
        'tong_tien_nhap' => 'integer',
    ];

    /**
     * Phiếu nhập này lấy hàng từ Nhà cung cấp nào?
     */
    public function nhaCungCap(): BelongsTo
    {
        return $this->belongsTo(NhaCungCap::class, 'nha_cung_cap_id', 'id');
    }

    /**
     * Ai là người lập Phiếu nhập này?
     */
    public function nguoiLap(): BelongsTo
    {
        return $this->belongsTo(NhanVien::class, 'nhan_vien_id', 'id');
    }

    /**
     * Danh sách các loại thuốc nhập trong phiếu này
     */
    public function chiTiet(): HasMany
    {
        return $this->hasMany(ChiTietPhieuNhap::class, 'phieu_nhap_id', 'id');
    }
}