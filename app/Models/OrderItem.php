<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'thuoc_id',
        'quantity',
        'price'
    ];

    protected $casts = [
        'price'    => 'integer',
        'quantity' => 'integer',
    ];

    /**
     * Quan hệ N-1: Dòng chi tiết này thuộc về Hóa đơn nào?
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    /**
     * Quan hệ N-1: Dòng chi tiết này là bán loại Thuốc nào?
     */
    public function thuoc(): BelongsTo
    {
        return $this->belongsTo(Thuoc::class, 'thuoc_id', 'id');
    }

    /**
     * Accessor: Tự động tính thành tiền của dòng này (Giá x Số lượng)
     * Cách gọi: $orderItem->thanh_tien
     */
    public function getThanhTienAttribute()
    {
        return $this->price * $this->quantity;
    }
}