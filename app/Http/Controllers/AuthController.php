<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Hiển thị form đăng nhập cho Nhân viên / Admin
     */
    public function showLogin()
    {
        // Nếu nhân viên đã đăng nhập trước đó thì chuyển hướng thẳng vào ca làm việc (POS)
        if (Auth::check()) {
            return redirect()->route('pos.index');
        }
        
        // Anh kiểm tra lại đường dẫn file view login của mình nằm ở đâu nhé
        // Nếu nằm ở resources/views/auth/login.blade.php thì để 'auth.login'
        // Nếu nằm ngoài cùng thư mục views thì để 'login'
        return view('auth.login'); 
    }

    /**
     * Xử lý sự kiện đăng nhập vào ca làm việc
     */
    public function login(Request $request)
    {
        // 1. Kiểm tra tính hợp lệ của dữ liệu đầu vào
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'Tài khoản Email không được để trống.',
            'email.email'       => 'Định dạng Email không hợp lệ.',
            'password.required' => 'Mật khẩu không được để trống.',
        ]);

        $remember = $request->has('remember');

        // 2. Thực hiện đăng nhập bằng Guard 'web' mặc định dành cho nội bộ cửa hàng
        if (Auth::attempt($credentials, $remember)) {
            // Tạo lại mã Session để chống tấn công giả mạo phiên
            $request->session()->regenerate();

            $user = Auth::user();

            // Chốt chặn kiểm tra trạng thái hoạt động của nhân viên (nếu có cột trang_thai trong DB)
            if (isset($user->trang_thai) && $user->trang_thai == 0) {
                Auth::logout();
                return back()->withErrors(['email' => 'Tài khoản này đã bị khóa hoặc ngừng kích hoạt.'])->onlyInput('email');
            }

            // 3. Phân luồng chuyển hướng thông minh sau khi vào ca
            // Nếu tài khoản có phân quyền (ví dụ cột vai_tro hoặc vai trò quản trị)
            if (isset($user->vai_tro) && $user->vai_tro === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            }

            // Mặc định nhân viên bán hàng đăng nhập xong sẽ vào thẳng màn hình POS
            return redirect()->intended(route('pos.index'));
        }

        // 4. Trả về thông báo lỗi nếu thông tin tài khoản mật khẩu sai
        return back()->withErrors([
            'email' => 'Tài khoản hoặc mật khẩu không chính xác.',
        ])->onlyInput('email');
    }

    /**
     * Xử lý đăng xuất hệ thống, kết thúc ca làm việc
     */
    public function logout(Request $request)
    {
        // Đăng xuất tài khoản nội bộ
        Auth::logout();

        // Xóa và làm mới toàn bộ Session dữ liệu ca làm việc cũ
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Quay về màn hình đăng nhập ban đầu
        return redirect()->route('login');
    }
}