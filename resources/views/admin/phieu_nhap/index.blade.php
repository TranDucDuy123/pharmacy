@extends('admin.layout')

@section('title', 'Phiếu Nhập - Admin')
@section('page_title', 'Quản lý Phiếu Nhập')

@section('content')
    @if(session('thong_bao'))
        <div class="mb-6 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-xl flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium text-sm">{{ session('thong_bao') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    @if(session('loi_he_thong'))
        <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-600 rounded-xl flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-medium text-sm">{{ session('loi_he_thong') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Tổng nhập tháng này</p>
            <h3 class="text-2xl font-black text-blue-600">
                {{ number_format($tongTienNhapThang ?? 0, 0, ',', '.') }}₫
            </h3>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Tổng số phiếu</p>
            <h3 class="text-2xl font-black text-slate-800">
                {{ $danhSachPhieuNhap->total() }}
            </h3>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Trạng thái mặc định</p>
            <h3 class="text-sm font-black text-emerald-600 bg-emerald-50 inline-flex px-3 py-2 rounded-xl">
                Đã nhập kho
            </h3>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex flex-wrap gap-4 justify-between items-center bg-white">
            <div class="flex items-center gap-4 flex-1">
                <form action="{{ route('phieu-nhap.index') }}" method="GET" class="relative max-w-sm w-full">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Tìm mã phiếu nhập..."
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </form>
            </div>

            <a href="{{ route('phieu-nhap.create') }}"
               class="px-5 py-2.5 bg-blue-600 text-white font-bold text-sm rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-100 flex items-center gap-2 no-underline">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tạo phiếu nhập
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/50 text-slate-500">
                    <tr>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[11px]">Mã phiếu</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[11px]">Nhà cung cấp</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[11px]">Người lập</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[11px] text-right">Tổng tiền</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[11px] text-center">Trạng thái</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[11px] text-center">Ngày tạo</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-[11px] text-center">Thao tác</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($danhSachPhieuNhap as $phieuNhap)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-800 text-sm group-hover:text-blue-600 transition">
                                        {{ $phieuNhap->ma_phieu }}
                                    </span>
                                    <span class="text-[11px] text-slate-400 font-medium">
                                        ID: #{{ $phieuNhap->id }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-700">
                                        {{ $phieuNhap->nhaCungCap->ten_ncc ?? 'N/A' }}
                                    </span>
                                    <span class="text-[11px] text-slate-400">
                                        {{ $phieuNhap->nhaCungCap->ma_ncc ?? '' }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="text-slate-600">
                                    {{ $phieuNhap->nguoiLap->ten_nhan_vien ?? $phieuNhap->nguoiLap->name ?? 'N/A' }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <span class="font-black text-blue-600 text-sm">
                                    {{ number_format($phieuNhap->tong_tien_nhap, 0, ',', '.') }}₫
                                </span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if($phieuNhap->trang_thai === 'completed')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-600">
                                        Đã nhập kho
                                    </span>
                                @elseif($phieuNhap->trang_thai === 'cancelled')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-red-50 text-red-600">
                                        Đã hủy
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-50 text-amber-600">
                                        Phiếu nháp
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="text-xs font-medium text-slate-500">
                                    {{ $phieuNhap->created_at ? $phieuNhap->created_at->format('d/m/Y H:i') : 'N/A' }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('phieu-nhap.show', $phieuNhap->id) }}"
                                   class="inline-flex items-center px-3 py-2 bg-slate-50 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition text-xs font-black no-underline">
                                    Chi tiết
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center text-slate-400 italic">
                                Không tìm thấy phiếu nhập phù hợp.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($danhSachPhieuNhap->hasPages())
            <div class="px-6 py-5 border-t border-slate-100 bg-slate-50/30">
                {{ $danhSachPhieuNhap->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection