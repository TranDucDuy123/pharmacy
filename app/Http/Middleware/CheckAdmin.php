<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra xem người dùng đã đăng nhập chưa VÀ có phải là Admin không
        // Hàm isAdmin() đã được bạn viết sẵn trong app/Models/NhanVien.php
        if (Auth::check() && Auth::user()->isAdmin()) {
            // Nếu đúng là Admin, cho phép đi tiếp vào Controller
            return $next($request);
        }

        // Nếu là nhân viên thường, đẩy về trang POS kèm thông báo lỗi
        // Hoặc đẩy về '/' (trang chủ) tùy ý bạn thay đổi route() ở dưới
        return redirect()->route('pos.index')->with('error', 'Bạn không có quyền truy cập trang Quản trị viên!');
    }
}