<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DYLY Pharma - Nhà thuốc trực tuyến</title>
     @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/pos.js'])
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body x-data="storefrontApp()" class="bg-slate-50 font-sans antialiased text-slate-900 flex flex-col min-h-screen">

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
                    <a href="#" @click.prevent="isCartOpen = true" class="relative p-2 text-slate-500 hover:text-blue-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <span x-show="cart.length > 0" x-cloak x-text="cart.length" class="absolute top-0 right-0 block h-4 w-4 rounded-full bg-red-500 text-white text-[9px] font-bold text-center leading-4">0</span>
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

    <!-- KHUNG GIỎ HÀNG TRƯỢT TỪ BÊN PHẢI (SLIDE-OVER CART) -->
    <div x-show="isCartOpen" style="display: none;" class="relative z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <div x-show="isCartOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <div x-show="isCartOpen" 
                         x-transition:enter="transform transition ease-in-out duration-300" 
                         x-transition:enter-start="translate-x-full" 
                         x-transition:enter-end="translate-x-0" 
                         x-transition:leave="transform transition ease-in-out duration-300" 
                         x-transition:leave-start="translate-x-0" 
                         x-transition:leave-end="translate-x-full" 
                         @click.away="isCartOpen = false"
                         class="pointer-events-auto w-screen max-w-md flex flex-col bg-white shadow-2xl">
                        
                        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50">
                            <h2 class="text-lg font-black text-slate-800 uppercase tracking-widest">Giỏ Hàng Của Bạn</h2>
                            <button @click="isCartOpen = false" class="p-2 text-slate-400 hover:text-slate-600 bg-white rounded-full shadow-sm">✕</button>
                        </div>

                        <div class="flex-1 overflow-y-auto p-6">
                            <template x-if="cart.length === 0">
                                <p class="text-center text-slate-400 italic mt-10">Chưa có sản phẩm nào. Cùng mua sắm nhé!</p>
                            </template>
                            
                            <div class="space-y-6">
                                <template x-for="(item, index) in cart" :key="item.id">
                                    <div class="flex gap-4 items-center border-b border-slate-50 pb-4">
                                        <div class="w-16 h-16 bg-slate-50 rounded-xl overflow-hidden border border-slate-100 shrink-0">
                                            <img x-show="item.image" :src="'/' + item.image" class="w-full h-full object-cover">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-sm font-bold text-slate-800 line-clamp-1" x-text="item.name"></h3>
                                            <p class="text-blue-600 font-black mt-1" x-text="formatPrice(item.price)"></p>
                                        </div>
                                        <div class="flex flex-col items-end gap-2">
                                            <button @click="removeFromCart(index)" class="text-[10px] font-bold text-red-500 bg-red-50 hover:bg-red-100 px-2 py-1 rounded transition">XÓA</button>
                                            <div class="flex items-center border border-slate-200 rounded-lg">
                                                <button @click="item.quantity > 1 ? item.quantity-- : null" class="w-6 h-6 flex items-center justify-center text-slate-500 hover:bg-slate-50">-</button>
                                                <span class="w-6 text-center text-xs font-bold" x-text="item.quantity"></span>
                                                <button @click="item.quantity < item.stock ? item.quantity++ : null" class="w-6 h-6 flex items-center justify-center text-slate-500 hover:bg-slate-50">+</button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="border-t border-slate-200 px-6 py-6 bg-slate-50">
                            <div class="flex justify-between text-base font-bold text-slate-900 mb-4">
                                <p>TỔNG CỘNG</p>
                                <p class="text-2xl text-blue-600" x-text="formatPrice(cartTotal)"></p>
                            </div>
                            <button @click="submitOrder()" 
                                    :disabled="cart.length === 0 || isProcessing"
                                    class="w-full rounded-2xl bg-blue-600 px-6 py-4 text-sm font-black text-white shadow-lg shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 transition-all disabled:opacity-50 flex items-center justify-center gap-2 tracking-widest uppercase">
                                <svg x-show="isProcessing" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="isProcessing ? 'Đang gửi đơn...' : 'Đặt hàng ngay'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPT LOGIC GIỎ HÀNG & ĐẶT HÀNG -->
    <script>
        function storefrontApp() {
            return {
                cart: [],
                isCartOpen: false,
                isProcessing: false,
                
                get headers() {
                    return {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Lấy trực tiếp từ Blade
                    }
                },

                init() {
                    let savedCart = localStorage.getItem('dyly_storefront_cart');
                    if (savedCart) {
                        this.cart = JSON.parse(savedCart);
                    }
                    this.$watch('cart', value => {
                        localStorage.setItem('dyly_storefront_cart', JSON.stringify(value));
                    });
                },

                addToCart(thuoc) {
                    if (thuoc.stock <= 0) {
                        alert('Sản phẩm này đã hết hàng!');
                        return;
                    }
                    let existing = this.cart.find(i => i.id === thuoc.id);
                    if (existing) {
                        if (existing.quantity < thuoc.stock) {
                            existing.quantity++;
                            this.isCartOpen = true;
                        } else {
                            alert('Số lượng đạt mức tồn kho tối đa!');
                        }
                    } else {
                        this.cart.push({ ...thuoc, quantity: 1 });
                        this.isCartOpen = true;
                    }
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                },

                get cartTotal() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                },

                formatPrice(price) {
                    return new Intl.NumberFormat('vi-VN').format(price || 0) + '₫';
                },

                async submitOrder() {
                    let isAuth = {{ Auth::guard('customer')->check() ? 'true' : 'false' }};
                    if (!isAuth) {
                        alert("Vui lòng đăng nhập để tiếp tục đặt hàng.");
                        window.location.href = "{{ route('customer.login') }}";
                        return;
                    }

                    this.isProcessing = true;
                    try {
                        let response = await fetch("{{ route('storefront.checkout') }}", {
                            method: 'POST',
                            headers: this.headers,
                            body: JSON.stringify({
                                cart: this.cart,
                                total_amount: this.cartTotal
                            })
                        });
                        
                        let data = await response.json();
                        
                        if (data.status === 'success') {
                            alert('🎉 ' + data.message);
                            this.cart = [];
                            this.isCartOpen = false;
                            window.location.reload(); // Tải lại trang để trừ số tồn kho trên giao diện
                        } else if (response.status === 401) {
                            window.location.href = "{{ route('customer.login') }}";
                        } else {
                            alert('❌ Lỗi: ' + data.message);
                        }
                    } catch (e) {
                        alert('Lỗi kết nối đến máy chủ!');
                    } finally {
                        this.isProcessing = false;
                    }
                }
            }
        }
    </script>
</body>
</html>