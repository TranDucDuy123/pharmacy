@extends('admin.layout')

@section('title', 'Kho Thuốc - Admin')
@section('page_title', 'Quản lý Kho Thuốc')

@section('content')
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

    @if(session('loi_he_thong'))
        <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-600 rounded-xl flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-medium text-sm">{{ session('loi_he_thong') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex flex-wrap gap-4 justify-between items-center bg-white">
            <div class="flex items-center gap-4 flex-1">
                <form action="{{ route('thuoc.index') }}" method="GET" class="relative max-w-sm w-full">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên thuốc, mã, hoạt chất..." 
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </form>
            </div>
            <a href="{{ route('thuoc.create') }}" class="px-5 py-2.5 bg-blue-600 text-white font-bold text-sm rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-100 flex items-center gap-2 no-underline">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Thêm thuốc mới
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/50 text-slate-500">
                    <tr>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[11px] w-12">Ảnh</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[11px]">Sản phẩm</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[11px]">Quy đổi</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[11px] text-right">Tồn kho</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[11px] text-right">Giá bán (Lẻ)</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[11px] text-center">Trạng thái</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[11px] text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($danhSachThuoc as $thuoc)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="px-6 py-4">
                                @if($thuoc->hinh_anh)
                                    <img src="{{ asset($thuoc->hinh_anh) }}" alt="Img" class="w-10 h-10 object-cover rounded-lg border border-slate-200">
                                @else
                                    <div class="w-10 h-10 bg-slate-100 rounded-lg border border-slate-200 flex items-center justify-center text-slate-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-800 text-sm group-hover:text-blue-600 transition">{{ $thuoc->ten_thuoc }}</span>
                                    <span class="text-[11px] text-slate-400 font-medium">Mã: {{ $thuoc->ma_thuoc }} | HSD: {{ $thuoc->han_su_dung ? $thuoc->han_su_dung->format('d/m/Y') : 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($thuoc->ty_le_quy_doi > 1)
                                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-md">
                                        1 {{ $thuoc->don_vi_nhap }} = {{ $thuoc->ty_le_quy_doi }} {{ $thuoc->don_vi_co_ban }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">Không quy đổi</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-black text-sm {{ $thuoc->so_luong_ton <= 10 ? 'text-red-600 bg-red-50 px-2 py-1 rounded' : 'text-slate-800' }}">
                                    <!-- SỬ DỤNG ACCESSOR TỪ MODEL -->
                                    {{ $thuoc->ton_kho_hien_thi }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-black text-blue-600 text-sm">
                                    {{ number_format($thuoc->gia_ban, 0, ',', '.') }}₫
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($thuoc->trang_thai)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-600">Đang bán</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-500">Ngừng</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('thuoc.edit', $thuoc->id) }}" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 morning 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></a>
                                    <form action="{{ route('thuoc.destroy', $thuoc->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thuốc này?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center text-slate-400 italic">Không tìm thấy dữ liệu thuốc phù hợp.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($danhSachThuoc->hasPages())
            <div class="px-6 py-5 border-t border-slate-100 bg-slate-50/30">
                {{ $danhSachThuoc->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection