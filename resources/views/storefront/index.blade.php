@extends('storefront.layout')

@section('content')
    <!-- Banner Gửi toa thuốc -->
    <div class="bg-blue-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <h1 class="text-2xl md:text-4xl font-black tracking-tight mb-2">Mua thuốc dễ dàng, giao hàng tận nơi!</h1>
                <p class="text-blue-100 font-medium max-w-xl">Bạn có đơn thuốc của bác sĩ? Hãy gửi cho chúng tôi, Dược sĩ DYLY sẽ tư vấn và lên đơn ngay cho bạn.</p>
            </div>
            <button class="px-6 py-3 bg-white text-blue-600 font-black rounded-full shadow-lg hover:bg-slate-50 hover:scale-105 transition-all w-full md:w-auto uppercase tracking-widest text-sm whitespace-nowrap">
                📷 Gửi Toa Thuốc Ngay
            </button>
        </div>
    </div>

    <!-- Khung hiển thị sản phẩm -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <!-- Menu Lọc Danh mục -->
        <div class="flex gap-2 overflow-x-auto pb-4 no-scrollbar mb-6">
            <a href="{{ route('storefront.home') }}" class="px-4 py-2 {{ !request('category') ? 'bg-blue-600 text-white shadow-md' : 'bg-white border border-slate-200 text-slate-600' }} text-sm font-bold rounded-full whitespace-nowrap transition text-decoration-none hover:-translate-y-0.5">
                Tất cả sản phẩm
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('storefront.home', ['category' => $cat]) }}" class="px-4 py-2 {{ request('category') == $cat ? 'bg-blue-600 text-white shadow-md' : 'bg-white border border-slate-200 text-slate-600 hover:border-blue-300' }} text-sm font-bold rounded-full whitespace-nowrap transition text-decoration-none hover:-translate-y-0.5">
                    {{ $cat }}
                </a>
            @endforeach
        </div>

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                Tủ Thuốc DYLY Pharma
            </h2>
        </div>

        <!-- Grid Sản phẩm -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6">
            @forelse($medicines as $thuoc)
                <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm hover:shadow-xl hover:border-blue-200 transition-all group flex flex-col h-full relative">
                    
                    @if($thuoc->loai_thuoc == 'Rx')
                        <div class="absolute top-2 left-2 z-10 bg-red-500 text-white text-[10px] font-black px-2 py-0.5 rounded shadow-sm">Thuốc kê đơn</div>
                    @endif

                    <div class="aspect-square bg-slate-50 rounded-xl mb-4 overflow-hidden flex items-center justify-center">
                        @if($thuoc->hinh_anh)
                            <img src="{{ asset($thuoc->hinh_anh) }}" alt="{{ $thuoc->ten_thuoc }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        @endif
                    </div>

                    <h3 class="font-bold text-slate-800 text-sm mb-1 leading-tight line-clamp-2 group-hover:text-blue-600 transition">{{ $thuoc->ten_thuoc }}</h3>
                    <p class="text-[11px] text-slate-400 mb-3 line-clamp-1 italic">{{ $thuoc->hoat_chat ?? 'Đang cập nhật' }}</p>

                    <div class="mt-auto pt-3 border-t border-slate-50 flex items-end justify-between">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 mb-0.5 uppercase">{{ $thuoc->don_vi_co_ban }}</p>
                            <p class="font-black text-blue-600 text-lg leading-none">{{ number_format($thuoc->gia_ban, 0, ',', '.') }}<span class="text-xs align-top">₫</span></p>
                        </div>
                        <button class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition shadow-sm active:scale-90" title="Thêm vào giỏ">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center flex flex-col items-center">
                    <svg class="w-16 h-16 text-slate-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    <p class="text-slate-500 font-medium">Chưa có sản phẩm nào phù hợp.</p>
                </div>
            @endforelse
        </div>

        <!-- Phân trang -->
        <div class="mt-10 flex justify-center">
            {{ $medicines->appends(request()->query())->links() }}
        </div>
    </div>
@endsection