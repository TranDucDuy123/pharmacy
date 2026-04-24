@extends('admin.layout')

@section('title', 'Lịch Sử Nhập Kho - Admin')
@section('page_title', 'Lịch Sử Nhập Kho')

@section('content')
    <!-- THỐNG KÊ NHANH -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-500 uppercase tracking-widest">Tiền nhập hàng (Tháng này)</p>
                <p class="text-2xl font-black text-slate-800">{{ number_format($tongTienNhapThang ?? 0, 0, ',', '.') }}₫</p>
            </div>
        </div>
    </div>

    <!-- THÔNG BÁO -->
    @if(session('thong_bao'))
        <div class="mb-6 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-xl flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-medium text-sm">{{ session('thong_bao') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        
        <!-- THANH CÔNG CỤ -->
        <div class="px-6 py-5 border-b border-slate-100 flex flex-wrap gap-4 justify-between items-center bg-white">
            <form action="{{ route('phieu-nhap.index') }}" method="GET" class="relative max-w-md w-full">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo mã phiếu nhập (VD: PN2024...)" 
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </form>

            <a href="{{ route('phieu-nhap.create') }}" class="px-5 py-2.5 bg-blue-600 text-white font-bold text-sm rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-200 flex items-center gap-2 no-underline">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tạo Phiếu Nhập
            </a>
        </div>
        
        <!-- BẢNG DỮ LIỆU -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/50 text-slate-500">
                    <tr>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[11px]">Mã Phiếu</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[11px]">Thời gian</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[11px]">Nhà Cung Cấp</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[11px]">Người Lập</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[11px] text-right">Tổng Tiền</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[11px] text-center">Trạng Thái</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[11px] text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($danhSachPhieuNhap as $phieu)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-md border border-blue-100">{{ $phieu->ma_phieu }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-medium text-slate-700">{{ $phieu->created_at->format('d/m/Y') }}</span>
                                    <span class="text-[11px] text-slate-400">{{ $phieu->created_at->format('H:i') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-800 line-clamp-1">{{ $phieu->nhaCungCap->ten_ncc ?? 'Không xác định' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-slate-600 flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold">{{ substr($phieu->nguoiLap->ho_ten ?? 'A', 0, 1) }}</div>
                                    {{ $phieu->nguoiLap->ho_ten ?? 'Hệ thống' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-black text-slate-800">{{ number_format($phieu->tong_tien_nhap, 0, ',', '.') }}₫</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($phieu->trang_thai == 'completed')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-700">
                                        Đã Nhập Kho
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-700">
                                        Đang Chờ
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('phieu-nhap.show', $phieu->id) }}" class="inline-flex items-center justify-center p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition" title="Xem chi tiết">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-slate-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                    <p class="text-slate-400 font-medium italic">Chưa có phiếu nhập kho nào.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- PHÂN TRANG -->
        @if($danhSachPhieuNhap->hasPages())
            <div class="px-6 py-5 border-t border-slate-100 bg-slate-50/30">
                {{ $danhSachPhieuNhap->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection