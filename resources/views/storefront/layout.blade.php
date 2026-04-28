<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DYLY Pharma - Nhà thuốc trực tuyến</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-900 flex flex-col min-h-screen">

    <!-- HEADER -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-lg flex items-center justify-center font-bold">D</div>
                    <a href="{{ route('storefront.home') }}" class="text-xl font-black text-blue-600 tracking-tight no-underline">DYLY Pharma</a>
                </div>

                <!-- Thanh tìm kiếm (Ẩn trên mobile) -->
                <div class="hidden md:block flex-1 max-w-lg mx-8">
                    <form action="{{ route('storefront.home') }}" method="GET" class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm thuốc, bệnh lý..." 
                            class="w-full pl-10 pr-4 py-2 bg-slate-100 border-none rounded-full focus:ring-2 focus:ring-blue-500 outline-none text-sm transition-all">
                        <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </form>
                </div>

                <!-- Menu Phải -->
                <div class="flex items-center gap-4">
                    <!-- Nút Giỏ hàng -->
                    <a href="#" class="relative p-2 text-slate-500 hover:text-blue-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <span class="absolute top-0 right-0 block h-4 w-4 rounded-full bg-red-500 text-white text-[9px] font-bold text-center leading-4">0</span>
                    </a>

                    <!-- Khu vực Đăng nhập / Khách hàng -->
                    <div class="pl-4 border-l border-slate-200">
                        @if(Auth::guard('customer')->check())
                            <!-- Đã đăng nhập -->
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="flex items-center gap-2 text-sm font-bold text-slate-700 hover:text-blue-600 transition">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                                        {{ substr(Auth::guard('customer')->user()->ten_khach_hang, 0, 1) }}
                                    </div>
                                    <span class="hidden sm:block">{{ Auth::guard('customer')->user()->ten_khach_hang }}</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                
                                <!-- Dropdown -->
                                <div x-show="open" @click.away="open = false" style="display: none;" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 py-2">
                                    <div class="px-4 py-2 border-b border-slate-50">
                                        <p class="text-xs text-slate-500">Điểm tích lũy</p>
                                        <p class="font-black text-emerald-600">{{ Auth::guard('customer')->user()->diem_tich_luy }} điểm</p>
                                    </div>
                                    <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-blue-600 no-underline">Đơn hàng của tôi</a>
                                    <form action="{{ route('customer.logout') }}" method="POST" class="block w-full">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-medium">Đăng xuất</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <!-- Chưa đăng nhập -->
                            <div class="flex items-center gap-2">
                                <a href="{{ route('customer.login') }}" class="text-sm font-bold text-slate-600 hover:text-blue-600 no-underline">Đăng nhập</a>
                                <a href="{{ route('customer.register') }}" class="hidden sm:block px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-full hover:bg-blue-700 transition shadow-md shadow-blue-200 no-underline">Đăng ký</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Thanh tìm kiếm Mobile -->
            <div class="md:hidden pb-3">
                <form action="{{ route('storefront.home') }}" method="GET" class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm thuốc..." class="w-full pl-10 pr-4 py-2 bg-slate-100 border-none rounded-full outline-none text-sm">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </form>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex-grow">
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="bg-slate-900 text-slate-300 py-12 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 bg-blue-500 text-white rounded-lg flex items-center justify-center font-bold">D</div>
                    <span class="text-xl font-black text-white tracking-tight">DYLY Pharma</span>
                </div>
                <p class="text-sm text-slate-400">Hệ thống nhà thuốc uy tín, cung cấp sản phẩm chất lượng với giá tốt nhất.</p>
            </div>
            <div>
                <h4 class="text-white font-bold mb-4 uppercase tracking-widest text-sm">Liên hệ</h4>
                <p class="text-sm mb-2">📍 123 Đường Y Tế, Phường Khỏe Mạnh, TP.HCM</p>
                <p class="text-sm mb-2">📞 1900 9999 (7h00 - 22h00)</p>
                <p class="text-sm">📧 cskh@dylypharma.vn</p>
            </div>
            <div>
                <h4 class="text-white font-bold mb-4 uppercase tracking-widest text-sm">Hướng dẫn</h4>
                <ul class="text-sm space-y-2">
                    <li><a href="#" class="hover:text-blue-400 transition text-decoration-none text-slate-300">Chính sách bảo mật</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition text-decoration-none text-slate-300">Quy định đổi trả</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition text-decoration-none text-slate-300">Gửi toa thuốc mua hàng</a></li>
                </ul>
            </div>
        </div>
    </footer>
</body>
</html>