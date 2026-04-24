<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NhanVien extends Authenticatable
{
    use Notifiable;

    protected $table = 'nhan_vien'; 

    protected $fillable = [
        'ma_nv', 
        'ho_ten', 
        'email', 
        'password', 
        'so_dien_thoai', 
        'chuc_vu', 
        'trang_thai'
    ];

    protected $hidden = [
        'password', 
        'remember_token',
    ];

    protected $casts = [
        'trang_thai' => 'boolean', // Tự động convert 0/1 thành true/false
    ];

    /**
     * Quan hệ 1-N: Một nhân viên (đặc biệt là NV Bán hàng) có thể tạo ra nhiều Hóa đơn.
     * Khóa ngoại lưu trong bảng orders là 'user_id'.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    /**
     * Scope kiểm tra Quyền: Quản trị viên
     */
    public function isAdmin(): bool
    {
        return $this->chuc_vu === 'admin';  
    }

    /**
     * Scope kiểm tra Quyền: Nhân viên bán hàng
     */
    public function isPosUser(): bool
    {
        return in_array($this->chuc_vu, ['Admin', 'Nhân viên bán hàng']);
    }
}