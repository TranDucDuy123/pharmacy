<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Thêm thẻ meta CSRF để bảo mật khi gọi API thanh toán bằng Javascript -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Bán hàng - DYLY Pharma POS</title>
    
    <!-- Thư viện hỗ trợ giao diện và logic -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/pos.js'])
    
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <style>
        /* Tùy chỉnh Phân trang: Thông báo ở trên - Nút ở dưới */
        .pagination-wrapper nav > div:last-child {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            gap: 15px !important;
        }
        .pagination-wrapper nav p { 
            text-align: center !important; 
            color: #94a3b8 !important; 
            font-size: 0.85rem !important; 
        }
        .pagination-wrapper nav span[aria-current="page"] span { 
            background-color: #2563eb !important; 
            color: white !important; 
            border-color: #2563eb !important; 
        }
        .pagination-wrapper nav a, .pagination-wrapper nav span { 
            padding: 8px 14px !important; 
            border-radius: 12px !important; 
            margin: 0 4px !important; 
            transition: all 0.2s; 
            border: 1px solid #e2e8f0 !important;
            text-decoration: none !important;
        }
        .pagination-wrapper nav a:hover { 
            background-color: #f8fafc !important; 
            color: #2563eb !important; 
            border-color: #3b82f6 !important; 
        }
        .pagination-wrapper nav > div:first-child { display: none !important; }

        /* Ép các nút phân trang tự động xuống dòng nếu quá dài để chống vỡ khung */
        .pagination-wrapper ul.pagination {
            flex-wrap: wrap !important;
            justify-content: center !important;
            gap: 5px;
        }

        /* Tiện ích cuộn */
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Hiệu ứng mượt cho Alpine */
        [x-cloak] { display: none !important; }
    </style>
</head>
<!-- Khởi tạo App AlpineJS tại thẻ body -->
<body class="bg-slate-100 h-screen flex flex-col antialiased overflow-hidden" x-data="posApp()">

    <!-- HEADER -->
    <header class="bg-white shadow-sm h-16 flex items-center justify-between px-6 shrink-0 z-30">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.dashboard') }}" class="p-2 bg-slate-100 rounded-lg text-slate-600 hover:bg-slate-200 transition decoration-none" title="Về trang Quản trị">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h1 class="text-xl font-bold text-blue-600 m-0 tracking-tight">💊 DYLY Pharma POS</h1>
        </div>
        <div class="flex items-center gap-6">
            <div class="text-sm font-medium text-slate-500">
                <span>{{ now()->format('H:i - d/m/Y') }}</span>
            </div>
            <div class="flex items-center gap-2 border-l pl-6">
                <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center font-bold shadow-sm">NV</div>
                <span class="text-sm font-semibold text-slate-700">{{ Auth::user()->ho_ten ?? 'Dược sĩ trực' }}</span>
                
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

    <main class="flex-1 flex overflow-hidden">
        
        <section class="flex-1 min-w-0 flex flex-col bg-slate-50 border-r border-slate-200 relative">
            
            <!-- KHU VỰC TÌM KIẾM VÀ LỌC -->
            <div class="p-4 bg-white border-b border-slate-200 shrink-0 z-20 shadow-sm">
                <form action="{{ route('pos.index') }}" method="GET" class="space-y-4">
                    <div class="flex gap-3">
                        <div class="relative flex-1">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên thuốc, hoạt chất hoặc quét mã vạch..." class="w-full pl-12 pr-4 py-3 bg-slate-100 border-none rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-700 placeholder-slate-400">
                            <svg class="w-6 h-6 text-slate-400 absolute left-4 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <button type="button" @click="openFilter = !openFilter" class="px-4 py-3 bg-slate-100 text-slate-600 rounded-xl border border-slate-200 hover:bg-slate-200 transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                            <span class="text-sm font-bold">Lọc</span>
                        </button>
                        <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition shadow-md">TÌM</button>
                    </div>

                    <!-- Tag Menu Danh mục động -->
                    <div class="flex gap-2 overflow-x-auto pb-1 no-scrollbar items-center">
                        <a href="{{ route('pos.index', array_merge(request()->query(), ['category' => 'Tất cả'])) }}" 
                           class="px-4 py-1.5 {{ request('category', 'Tất cả') == 'Tất cả' ? 'bg-blue-600 text-white shadow-md' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }} text-xs font-bold rounded-full whitespace-nowrap transition decoration-none">
                           Tất cả
                        </a>
                        @foreach($dsDanhMuc as $dm)
                            <a href="{{ route('pos.index', array_merge(request()->except('page'), ['category' => $dm])) }}" 
                               class="px-4 py-1.5 text-xs font-bold rounded-full whitespace-nowrap transition-all duration-200 decoration-none
                               {{ request('category') == $dm ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'bg-white border border-slate-200 text-slate-500 hover:border-blue-400 hover:text-blue-600' }}">
                               {{ $dm }}
                            </a>
                        @endforeach
                    </div>

                    <!-- Bảng lọc nâng cao (Alpine.js) -->
                    <div x-show="openFilter" x-cloak x-transition class="p-4 mt-2 bg-slate-50 rounded-xl border border-slate-200 grid grid-cols-1 md:grid-cols-3 gap-4 shadow-inner">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">Khoảng giá (VNĐ)</label>
                            <div class="flex items-center gap-2">
                                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Từ..." class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs">
                                <span class="text-slate-300">-</span>
                                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Đến..." class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">Quy chế & Kho</label>
                            <div class="flex gap-2">
                                <select name="loai" class="flex-1 px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs">
                                    <option value="">-- Loại thuốc --</option>
                                    <option value="OTC" {{ request('loai') == 'OTC' ? 'selected' : '' }}>OTC (Không đơn)</option>
                                    <option value="Rx" {{ request('loai') == 'Rx' ? 'selected' : '' }}>Rx (Kê đơn)</option>
                                </select>
                                <select name="stock" class="flex-1 px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs">
                                    <option value="">-- Tồn kho --</option>
                                    <option value="sap_het" {{ request('stock') == 'sap_het' ? 'selected' : '' }}>Sắp hết (<=10)</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="flex-1 py-2 bg-blue-600 text-white rounded-lg font-black text-xs hover:bg-blue-700">ÁP DỤNG LỌC</button>
                            <a href="{{ route('pos.index') }}" class="px-4 py-2 bg-slate-200 text-slate-600 rounded-lg font-bold text-xs decoration-none">XÓA</a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- DANH SÁCH THUỐC -->
            <div class="flex-1 overflow-y-auto overflow-x-hidden p-4 custom-scrollbar">
                @if($danhSachThuoc->isEmpty())
                    <div class="h-64 flex flex-col items-center justify-center text-slate-400 italic">
                        <svg class="w-16 h-16 mb-4 opacity-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <p>Không tìm thấy thuốc nào phù hợp.</p>
                        <a href="{{ route('pos.index') }}" class="mt-2 text-blue-600 font-bold underline decoration-none">Hiển thị tất cả</a>
                    </div>
                @else
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($danhSachThuoc as $thuoc)
                        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 hover:border-blue-500 hover:shadow-md transition flex flex-col h-full group relative overflow-hidden">
                            <!-- Nhãn thuốc kê đơn -->
                            @if($thuoc->loai_thuoc == 'Rx')
                                <div class="absolute top-0 right-0 bg-red-500 text-white text-[9px] font-black px-3 py-1 rounded-bl-xl shadow-sm">Rx</div>
                            @endif

                            <div class="flex justify-between items-start mb-2">
                                <span class="text-[10px] font-black {{ $thuoc->so_luong_ton <= 10 ? 'text-red-600 bg-red-50' : 'text-emerald-600 bg-emerald-50' }} px-2 py-0.5 rounded-lg">
                                    Tồn: {{ $thuoc->so_luong_ton }}
                                </span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">{{ $thuoc->don_vi_tinh }}</span>
                            </div>
                            
                            <h3 class="font-bold text-slate-800 leading-tight mb-1 text-sm group-hover:text-blue-600 transition">{{ $thuoc->ten_thuoc }}</h3>
                            <p class="text-[11px] text-slate-400 mb-3 line-clamp-1 italic" title="{{ $thuoc->hoat_chat }}">
                                {{ $thuoc->hoat_chat ?? 'Thông tin hoạt chất chưa cập nhật' }}
                            </p>
                            
                            <div class="flex justify-between items-end mt-auto pt-3 border-t border-slate-50">
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-slate-400 line-through font-medium">
                                        {{ number_format($thuoc->gia_ban * 1.1, 0, ',', '.') }}₫
                                    </span>
                                    <span class="font-black text-blue-600 text-base leading-none">
                                        {{ number_format($thuoc->gia_ban, 0, ',', '.') }}₫
                                    </span>
                                </div>
                                <!-- NÚT THÊM VÀO GIỎ -->
                                <button 
                                    @click="addToCart({ 
                                        id: {{ $thuoc->id }}, 
                                        name: '{{ str_replace("'", "\'", $thuoc->ten_thuoc) }}', 
                                        price: {{ $thuoc->gia_ban }}, 
                                        stock: {{ $thuoc->so_luong_ton }},
                                        unit: '{{ $thuoc->don_vi_tinh }}'
                                    })"
                                    class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center hover:bg-blue-600 hover:text-white transition transform active:scale-90 shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- PHÂN TRANG -->
                    <div class="mt-16 mb-12 pagination-wrapper w-full overflow-x-auto">
                        {{ $danhSachThuoc->links() }}
                    </div>
                @endif
            </div>
        </section>

        <!-- ASIDE GIỎ HÀNG -->
        <aside class="bg-white flex flex-col shrink-0 relative z-20 shadow-2xl">
            <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h2 class="font-black text-slate-800 text-sm uppercase tracking-widest flex items-center gap-2">
                    Giỏ hàng 
                    <!-- Hiển thị số lượng món trong giỏ -->
                    <span x-show="cart.length > 0" x-cloak class="bg-blue-600 text-white text-[10px] px-2 py-0.5 rounded-full" x-text="cart.length"></span>
                </h2>
                <span class="bg-blue-100 text-blue-600 text-[10px] px-2 py-1 rounded-full font-black">#POS-{{ date('His') }}</span>
            </div>

            <!-- HIỂN THỊ KHI GIỎ HÀNG TRỐNG -->
            <div x-show="cart.length === 0" class="flex-1 overflow-y-auto p-8 flex flex-col items-center justify-center text-slate-300">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 opacity-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <p class="text-xs italic text-center text-slate-400 px-6">Giỏ hàng đang trống. Hãy chọn thuốc từ danh sách hoặc quét mã vạch để bắt đầu.</p>
            </div>

            <!-- HIỂN THỊ KHI CÓ SẢN PHẨM -->
            <div x-show="cart.length > 0" x-cloak class="flex-1 overflow-y-auto p-4 custom-scrollbar bg-slate-50/30">
                <div class="space-y-3">
                    <template x-for="item in cart" :key="item.id">
                        <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs font-bold text-slate-800 truncate" x-text="item.name"></h4>
                                <div class="text-[10px] text-slate-400 mt-1 flex items-center gap-2">
                                    <span x-text="item.unit"></span>
                                    <span>•</span>
                                    <span class="font-semibold text-blue-600" x-text="formatPrice(item.price)"></span>
                                </div>
                            </div>
                            
                            <!-- Nút tăng giảm số lượng -->
                            <div class="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-lg p-1">
                                <button @click="decreaseQuantity(item.id)" class="w-6 h-6 flex items-center justify-center text-slate-500 hover:bg-slate-200 rounded-md transition">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                </button>
                                <span class="text-xs font-black w-4 text-center" x-text="item.quantity"></span>
                                <button @click="increaseQuantity(item.id)" class="w-6 h-6 flex items-center justify-center text-slate-500 hover:bg-slate-200 rounded-md transition">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-200">
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-slate-500 text-xs">TẠM TÍNH</span>
                        <span class="font-bold text-slate-700" x-text="formatPrice(totalAmount)"></span>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-slate-200">
                        <span class="font-black text-slate-800 text-xs uppercase tracking-tighter">Khách cần trả</span>
                        <span class="font-black text-3xl text-blue-600 tracking-tighter" x-text="formatPrice(totalAmount)"></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <button @click="clearCart()" class="py-3 bg-white border border-slate-200 text-slate-400 font-bold rounded-2xl hover:bg-red-50 hover:text-red-500 hover:border-red-100 transition">HỦY</button>
                    
                    <!-- NÚT THANH TOÁN (Đã liên kết với hàm processCheckout) -->
                    <button 
                        @click="processCheckout()" 
                        :disabled="cart.length === 0 || isProcessing"
                        class="py-3 bg-blue-600 text-white font-black rounded-2xl shadow-xl shadow-blue-200 hover:bg-blue-700 transition tracking-widest uppercase text-xs disabled:opacity-50 flex justify-center items-center gap-2">
                        
                        <!-- Hiển thị Loading Spinner khi đang xử lý API -->
                        <svg x-show="isProcessing" x-cloak class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        
                        <span x-text="isProcessing ? 'ĐANG XỬ LÝ...' : 'THANH TOÁN'"></span>
                    </button>
                </div>
            </div>
        </aside>

    </main>
</body>
</html>