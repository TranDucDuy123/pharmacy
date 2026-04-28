<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KhachHang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerAuthController extends Controller
{
    public function showLogin()
    {
        return view('storefront.auth.login');
    }

    public function showRegister()
    {
        return view('storefront.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'ten_khach_hang' => 'required|string|max:255',
            'so_dien_thoai'  => 'required|string|max:20|unique:khach_hang,so_dien_thoai',
            'password'       => 'required|string|min:6|confirmed', // Cần input password_confirmation
        ], [
            'so_dien_thoai.unique' => 'Số điện thoại này đã được đăng ký.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        // Tạo mã khách hàng tự động
        $lastKh = KhachHang::orderBy('id', 'desc')->first();
        $nextId = $lastKh ? $lastKh->id + 1 : 1;
        $maKh = 'KH' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        // Tạo khách hàng mới với trạng thái 0 (Chờ duyệt)
        KhachHang::create([
            'ma_khach_hang'  => $maKh,
            'ten_khach_hang' => $request->ten_khach_hang,
            'so_dien_thoai'  => $request->so_dien_thoai,
            'password'       => Hash::make($request->password),
            'diem_tich_luy'  => 0,
            'trang_thai'     => 0, // Bắt buộc chờ Admin gạt nút duyệt
        ]);

        return redirect()->route('customer.login')->with('success', 'Đăng ký thành công! Vui lòng chờ Admin duyệt tài khoản để đăng nhập.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'so_dien_thoai' => 'required|string',
            'password'      => 'required|string',
        ]);

        // Sử dụng Guard 'customer' mà chúng ta đã cấu hình trong config/auth.php
        if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::guard('customer')->user();
            
            // Kiểm tra trạng thái phê duyệt
            if ($user->trang_thai == 0) {
                Auth::guard('customer')->logout();
                return back()->withInput()->with('error', 'Tài khoản của bạn đang chờ Admin xét duyệt. Vui lòng liên hệ Hotline!');
            }

            return redirect()->route('storefront.home');
        }

        return back()->withInput()->with('error', 'Số điện thoại hoặc mật khẩu không chính xác.');
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('storefront.home');
    }
}