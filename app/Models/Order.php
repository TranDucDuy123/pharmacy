<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'customer_id', // Có thể null nếu khách vãng lai
        'total_price',
        'user_id',     // ID của Nhân viên bán hàng
        'note',
        'status'       // 'completed', 'cancelled', 'pending'
    ];

    protected $casts = [
        'total_price' => 'integer', // Dùng integer cho VNĐ cho đồng bộ với bảng Thuốc
        'created_at'  => 'datetime',
    ];

    /**
     * Quan hệ 1-N: Một hóa đơn có nhiều mặt hàng bên trong
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    /**
     * Quan hệ N-1: Hóa đơn này do Nhân viên nào tạo ra?
     */
    public function nhanVien(): BelongsTo
    {
        // Liên kết trường 'user_id' trong bảng orders với bảng 'nhan_vien'
        return $this->belongsTo(NhanVien::class, 'user_id', 'id');
    }

     // Tạo mối quan hệ lấy thông tin khách hàng từ hóa đơn
    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'khach_hang_id');
    }
}