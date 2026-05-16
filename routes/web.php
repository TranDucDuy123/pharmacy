<?php

use Illuminate\Support\Facades\Route;

// ==========================================
// PHẦN 1: IMPORT CÁC CONTROLLER SẼ SỬ DỤNG
// ==========================================
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\AdminThuocController;
use App\Http\Controllers\Api\ApiPosController;
use App\Http\Middleware\CheckAdmin;

use App\Http\Controllers\KhachHangController;
use App\Http\Controllers\NhaCungCapController;
use App\Http\Controllers\PhieuNhapController;

use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\CustomerAuthController;

// ==========================================
// PHẦN 2: CÁC ROUTE CÔNG KHAI (Không cần đăng nhập)
// ==========================================
// Route::get('/', function () {
//     return view('welcome');
// });

// Giao diện cửa hàng khách hàng (Đã đổi tên thành storefront.home cho khớp với View)
Route::get('/', [StorefrontController::class, 'index'])->name('storefront.home');

// Auth Khách Hàng
Route::get('/khach-hang/login', [CustomerAuthController::class, 'showLogin'])->name('customer.login');
Route::post('/khach-hang/login', [CustomerAuthController::class, 'login'])->name('customer.login.post');
Route::get('/khach-hang/register', [CustomerAuthController::class, 'showRegister'])->name('customer.register');
Route::post('/khach-hang/register', [CustomerAuthController::class, 'register'])->name('customer.register.post');
Route::post('/khach-hang/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');

// Hiển thị form đăng nhập & Xử lý đăng nhập
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);


// ==========================================
// PHẦN 3: CÁC ROUTE CẦN ĐĂNG NHẬP (Bảo vệ vòng ngoài)
// ==========================================
Route::middleware(['auth'])->group(function () {
    
    // Đăng xuất
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Màn hình POS (Dành cho nhân viên bán hàng)
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::get('/pos/orders/{id}/print', [PosController::class, 'printBill'])->name('pos.print');

    // --- NHÓM ROUTE QUẢN TRỊ (Bảo vệ vòng trong bằng CheckAdmin) ---
    Route::middleware(CheckAdmin::class)->prefix('admin')->group(function () {
        
        // Trang Tổng quan Dashboard
        Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');

        // Hóa Đơn bán
        Route::get('/orders', [AdminController::class, 'ordersIndex'])->name('admin.orders.index');
        Route::get('/orders/{id}', [AdminController::class, 'orderShow'])->name('admin.orders.show');
        
        // MODULE KHÁCH HÀNG
        Route::resource('khach-hang', KhachHangController::class);

        Route::resource('nha-cung-cap', NhaCungCapController::class);
        
        //có kiểm soát không cho sửa đơn
        Route::resource('phieu-nhap', PhieuNhapController::class)
    ->only(['index', 'create', 'store', 'show']);
        
        // Quản lý Kho Thuốc
        Route::resource('thuoc', AdminThuocController::class);
        
    });

    // --- NHÓM ROUTE API DÀNH CHO MÀN HÌNH POS ---
    Route::prefix('api/v1/pos')->group(function () {
        // API Lấy danh sách thuốc
        Route::get('/medicines', [ApiPosController::class, 'getMedicines']);
        // API Thanh toán đơn hàng
        Route::post('/checkout', [ApiPosController::class, 'checkout']);
        // (Nếu có) API Tìm và Tạo khách hàng
        Route::get('/customers/search', [ApiPosController::class, 'searchCustomer']);
        Route::post('/customers/create', [ApiPosController::class, 'createCustomer']);
    });

}); 

// --- NHÓM ROUTE API CHỈ DÀNH CHO ADMIN ---
Route::middleware(['auth', CheckAdmin::class])->prefix('api/v1/admin')->group(function () {
    // API Lấy danh sách đơn hàng
    Route::get('/orders', [ApiPosController::class, 'index']);
});

// Bắt buộc khách hàng phải đăng nhập mới được gọi API này
Route::middleware(['auth:customer'])->group(function () {
    Route::post('/storefront/checkout', [StorefrontController::class, 'checkout'])->name('storefront.checkout');
});