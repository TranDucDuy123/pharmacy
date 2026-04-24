@extends('admin.layout')

@section('title', 'Hóa Đơn Bán - Admin')
@section('page_title', 'Quản lý Hóa Đơn Bán Hàng')

@section('content')
    <!-- HIỂN THỊ THÔNG BÁO -->
    @if(session('thong_bao'))
        <div class="mb-6 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-xl flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-medium text-sm">{{ session('thong_bao') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        
        <!-- THANH CÔNG CỤ & TÌM KIẾM -->
        <div class="px-6 py-5 border-b border-slate-100 flex flex-wrap gap-4 justify-between items-center bg-white">
            <div class="flex items-center gap-3 flex-1">
                <form action="{{ route('admin.orders.index') }}" method="GET" class="relative max-w-xs w-full">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm mã hóa đơn (VD: 0015)..." 
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </form>

                <div class="relative hidden sm:block">
                    <!-- Form Lọc theo ngày (Có thể bọc trong cùng form search hoặc form riêng) -->
                    <form action="{{ route('admin.orders.index') }}" method="GET" class="flex gap-2">
                        <input type="date" name="date" value="{{ request('date') }}" class="pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none text-slate-600">
                        <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <button type="submit" class="px-3 py-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 font-medium text-sm transition hidden md:block">Lọc</button>
                    </form>
                </div>
            </div>

            <button class="px-5 py-2.5 bg-slate-800 text-white font-bold text-sm rounded-xl hover:bg-slate-900 transition shadow-lg shadow-slate-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Xuất Báo Cáo
            </button>
        </div>
        
        <!-- BẢNG LỊCH SỬ GIAO DỊCH -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/50 text-slate-500">
                    <tr>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[11px]">Mã HĐ</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[11px]">Thời gian lập</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[11px]">Người lập (Nhân viên)</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[11px] text-right">Tổng thanh toán</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[11px] text-center">Trạng thái</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[11px] text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($danhSachHoaDon as $hoaDon)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            
                            <!-- Mã Hóa Đơn -->
                            <td class="px-6 py-4">
                                <span class="font-bold text-blue-600 text-sm bg-blue-50 px-2 py-1 rounded-md border border-blue-100">
                                    #ORD-{{ str_pad($hoaDon->id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>
                            
                            <!-- Thời gian -->
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-slate-700 font-medium">{{ $hoaDon->created_at->format('d/m/Y') }}</span>
                                    <span class="text-[11px] text-slate-400 font-medium">{{ $hoaDon->created_at->format('H:i') }}</span>
                                </div>
                            </td>
                            
                            <!-- Người lập -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 font-bold text-xs uppercase">
                                        {{ substr($hoaDon->nhanVien->ho_ten ?? 'U', 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-slate-700 font-bold text-sm line-clamp-1">
                                            {{ $hoaDon->nhanVien->ho_ten ?? 'Tài khoản đã xóa' }}
                                        </span>
                                        <span class="text-[11px] text-slate-400">
                                            {{ ($hoaDon->nhanVien->chuc_vu ?? '') == 'Admin' ? 'Quản trị viên' : 'Bán tại quầy' }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Tổng thanh toán -->
                            <td class="px-6 py-4 text-right">
                                <span class="font-black text-slate-800 text-sm">
                                    {{ number_format($hoaDon->total_price, 0, ',', '.') }}₫
                                </span>
                            </td>

                            <!-- Trạng thái -->
                            <td class="px-6 py-4 text-center">
                                @if($hoaDon->status == 'completed')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Hoàn thành
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-50 text-amber-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Đang xử lý
                                    </span>
                                @endif
                            </td>
                            
                            <!-- Thao tác -->
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.orders.show', $hoaDon->id) }}" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition tooltip" title="Xem chi tiết">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    
                                    {{-- <button type="button" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition" title="In hóa đơn" onclick="window.print()">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    </button> --}}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="p-4 bg-slate-50 rounded-full mb-4">
                                        <svg class="w-12 h-12 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                    </div>
                                    <p class="text-slate-400 font-medium italic">Không tìm thấy hóa đơn nào phù hợp.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- PHÂN TRANG -->
        @if(isset($danhSachHoaDon) && $danhSachHoaDon->hasPages())
            <div class="px-6 py-5 border-t border-slate-100 bg-slate-50/30">
                {{ $danhSachHoaDon->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection