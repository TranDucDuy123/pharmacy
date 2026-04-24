<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Dòng này bắt buộc phải có để dùng các hàm Auth::

class AuthController extends Controller
{
    // 1. Hiện giao diện đăng nhập
    public function showLogin()
    {
        return view('auth.login');
    }

    // 2. Xử lý khi user bấm nút "Đăng nhập"
    public function login(Request $request)
    {
        // Yêu cầu nhập đủ 2 trường
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Thử đăng nhập
        if (Auth::attempt($credentials)) {
            $user = Auth::user(); // Lấy thông tin người vừa đăng nhập

            // Nếu tài khoản đã bị khóa (Nghỉ việc)
            if ($user->trang_thai == 0) {
                Auth::logout();
                return back()->withErrors(['email' => 'Tài khoản của bạn đã bị khóa.']);
            }

            $request->session()->regenerate();

            // PHÂN QUYỀN
            if ($user->chuc_vu === 'admin') {
                return redirect()->route('admin.dashboard'); // Admin vào kho
            } else {
                return redirect()->route('pos.index'); // Nhân viên ra quầy
            }
        }

        // Nếu gõ sai pass hoặc email
        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác.',
        ]);
    }

    // 3. Xử lý Đăng xuất
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}