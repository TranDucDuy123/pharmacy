<?php

namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;
    use Illuminate\Database\Eloquent\Relations\HasMany;

    class KhachHang extends Authenticatable
    {
        use HasFactory, Notifiable;

        protected $table = 'khach_hang';

        protected $fillable = [
            'ma_khach_hang',
            'ten_khach_hang',
            'so_dien_thoai',
            'password',
            'diem_tich_luy',
            'ghi_chu',
            'trang_thai'
        ];

        // Ẩn mật khẩu và token khi trả về dữ liệu (Bảo mật)
        protected $hidden = [
            'password',
            'remember_token',
        ];

        protected $casts = [
            'trang_thai' => 'boolean',
        ];

        /**
        * Khách hàng này có những hóa đơn nào?
        */
        public function orders(): HasMany
        {
            return $this->hasMany(Order::class, 'khach_hang_id', 'id');
        }
    }