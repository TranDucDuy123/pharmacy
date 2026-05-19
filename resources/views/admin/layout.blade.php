<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - Dyly Pharma System')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/pos.js'])
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Thêm CSS nhỏ để làm thanh cuộn gọn gàng giống màn hình POS -->
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-900">

    <div class="flex h-screen overflow-hidden">

        <!-- SIDEBAR -->
        <aside class="w-64 bg-slate-800 text-white flex flex-col shrink-0 z-20">
            <div class="flex items-center justify-center h-16 border-b border-slate-700">
                <span class="text-2xl font-bold text-blue-400">💊 Dyly Pharma</span>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto custom-scrollbar">
                <!-- Menu Tổng quan -->
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Tổng quan
                </a>
                
                <!-- Menu Kho Thuốc -->
                <!-- Thay đổi href trỏ tới danh sách thuốc, kiểm tra URL chứa 'thuoc' để bật sáng menu -->
                <a href="/admin/thuoc" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/thuoc*') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Kho Thuốc
                </a>
                
                <a href="/admin/orders" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/orders*') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-700' }} no-underline">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    Hóa Đơn bán
                </a>

                 <a href="/admin/phieu-nhap" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/phieu-nhap*') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-700' }} no-underline">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    Phiếu Nhập
                </a>
                <a href="/admin/khach-hang" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/khach-hang*') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-700' }} no-underline">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Khách Hàng
                </a>

                 <a href="/admin/nha-cung-cap" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/nha-cung-cap*') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-700' }} no-underline">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Nhà Cung Cấp
                </a>
            </nav>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col overflow-y-auto">
            
            <!-- HEADER -->
            <header class="bg-white shadow-sm h-16 flex items-center justify-between px-8 z-10 shrink-0">
                <h2 class="text-xl font-semibold text-slate-800">@yield('page_title', 'Bảng Điều Khiển')</h2>
                
                <div class="flex items-center gap-4">
                    <!-- SỬA: Thay text-decoration-none thành no-underline của Tailwind -->
                    <a href="{{ route('pos.index') }}" target="_blank" class="px-4 py-2 bg-blue-100 text-blue-700 font-bold rounded-lg hover:bg-blue-200 transition text-sm no-underline">
                        Mở màn hình POS
                    </a>
                    
                    <button class="text-slate-500 hover:text-blue-600 relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span class="absolute top-0 right-0 block h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white"></span>
                    </button>
                    <div class="flex items-center gap-2 cursor-pointer border-l pl-4 border-slate-200">
                        <img class="h-8 w-8 rounded-full border border-slate-200" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->ho_ten ?? 'Admin') }}&background=0D8ABC&color=fff" alt="Avatar">
                        <span class="text-sm font-medium text-slate-700">
                            {{ Auth::user()->ho_ten ?? 'Admin' }} 
                            @if(isset(Auth::user()->ma_nv)) - {{ Auth::user()->ma_nv }} @endif
                        </span>
                        <!-- NÚT ĐĂNG XUẤT (TÙY CHỈNH ICON TẠI ĐÂY) -->
                 <form action="{{ route('logout') }}" method="POST" class="ml-3 m-0 flex items-center">
                    @csrf
                    <button type="submit" onclick="return confirm('Bạn có chắc chắn muốn đăng xuất khỏi hệ thống?');" class="p-1 hover:bg-red-50 rounded-xl transition flex items-center justify-center" title="Đăng xuất">
                        <!-- 
                            GIẢ SỬ: Bạn đã tải ảnh về và bỏ vào: public/assets/images/logout.png 
                            Hàm asset() sẽ tự động trỏ vào thư mục public.
                        -->
                        <img src="{{ asset('./storage/logout.png') }}" 
                             alt="Logout" 
                             class="w-7 h-7 object-contain"
                             onerror="this.src='https://cdn-icons-png.flaticon.com/512/1286/1286853.png'"> 
                             <!-- Dòng onerror ở trên là fallback: nếu ko tìm thấy file nội bộ nó sẽ hiện icon online -->
                    </button>
                </form>
                    </div>
                </div>
            </header>

            <!-- DYNAMIC CONTENT -->
            <main class="p-8">
                @yield('content')
            </main>
            
        </div>
    </div>
</body>
</html>