<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cổng Hệ Thống - Dyly Pharma</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen antialiased">

    <div class="max-w-4xl w-full px-6">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-extrabold text-slate-800 tracking-tight flex items-center justify-center gap-3">
                <span class="text-5xl">💊</span> Dyly Pharma System
            </h1>
            <p class="text-slate-500 mt-3 text-lg">Vui lòng chọn không gian làm việc của bạn</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <a href="{{ route('login') }}" class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-200 overflow-hidden transform hover:-translate-y-1">
                <div class="h-3 bg-emerald-500 w-full"></div>
                <div class="p-8">
                    <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800 mb-2">Bán hàng tại quầy</h2>
                    <p class="text-slate-500 mb-6 line-clamp-2">Giao diện thao tác nhanh (POS) dành cho nhân viên. Quét mã vạch, lên đơn, tính tiền và in hoá đơn tức thì.</p>
                    <div class="flex items-center text-emerald-600 font-semibold">
                        Mở màn hình POS 
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </div>
                </div>
            </a>

            <a href="{{ route('login') }}" class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-200 overflow-hidden transform hover:-translate-y-1">
                <div class="h-3 bg-blue-600 w-full"></div>
                <div class="p-8">
                    <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800 mb-2">Quản trị hệ thống</h2>
                    <p class="text-slate-500 mb-6 line-clamp-2">Kiểm soát kho thuốc, đối tác, nhập/xuất kho, thống kê doanh thu và quản lý tài khoản nhân viên.</p>
                    <div class="flex items-center text-blue-600 font-semibold">
                        Vào trang Quản trị 
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </div>
                </div>
            </a>

        </div>
        
        <div class="text-center mt-12 text-sm text-slate-400">
            Hệ thống yêu cầu đăng nhập trước khi sử dụng.
        </div>
    </div>

</body>