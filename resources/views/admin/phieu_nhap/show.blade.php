@extends('admin.layout')

@section('title', 'Chi Tiết Phiếu Nhập')
@section('page_title', 'Chi Tiết Phiếu Nhập: ' . $phieuNhap->ma_phieu)

@section('content')
    <div class="mb-6 flex flex-wrap gap-4 justify-between items-center print:hidden">
        <a href="{{ route('phieu-nhap.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 font-bold text-sm rounded-xl hover:bg-slate-50 transition flex items-center gap-2 no-underline shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại danh sách
        </a>
        <button onclick="window.print()" class="px-5 py-2 bg-slate-800 text-white font-bold text-sm rounded-xl hover:bg-slate-900 transition shadow-lg shadow-slate-200 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            In Phiếu Nhập
        </button>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden max-w-5xl mx-auto print:shadow-none print:border-none print:m-0">
        
        <!-- HEADER PHIẾU -->
        <div class="p-8 border-b border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h2 class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-4">Thông tin Nhà cung cấp</h2>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <p class="font-black text-slate-800 text-base mb-1">{{ $phieuNhap->nhaCungCap->ten_ncc ?? 'N/A' }}</p>
                    <p class="text-sm text-slate-500 mb-1"><span class="font-semibold text-slate-700">Mã NCC:</span> {{ $phieuNhap->nhaCungCap->ma_ncc ?? '-' }}</p>
                    <p class="text-sm text-slate-500 mb-1"><span class="font-semibold text-slate-700">SĐT:</span> {{ $phieuNhap->nhaCungCap->so_dien_thoai ?? '-' }}</p>
                    <p class="text-sm text-slate-500"><span class="font-semibold text-slate-700">Địa chỉ:</span> {{ $phieuNhap->nhaCungCap->dia_chi ?? '-' }}</p>
                </div>
            </div>
            <div class="md:text-right">
                <h2 class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-4">Thông tin Chứng từ</h2>
                <h1 class="text-2xl font-black text-blue-600 tracking-tight mb-2">{{ $phieuNhap->ma_phieu }}</h1>
                <p class="text-sm text-slate-500 mb-1"><span class="font-semibold text-slate-700">Thời gian nhập:</span> {{ $phieuNhap->created_at->format('d/m/Y H:i') }}</p>
                <p class="text-sm text-slate-500 mb-1"><span class="font-semibold text-slate-700">Người lập phiếu:</span> {{ $phieuNhap->nguoiLap->ho_ten ?? 'Hệ thống' }}</p>
                <p class="text-sm text-slate-500">
                    <span class="font-semibold text-slate-700">Trạng thái:</span> 
                    <span class="text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded">Đã nhập vào kho</span>
                </p>
            </div>
        </div>

        @if($phieuNhap->ghi_chu)
        <div class="px-8 py-4 bg-amber-50/50 border-b border-slate-100">
            <p class="text-[11px] font-black text-amber-600 uppercase tracking-widest mb-1">Ghi chú chứng từ:</p>
            <p class="text-sm text-slate-700 font-medium italic">{{ $phieuNhap->ghi_chu }}</p>
        </div>
        @endif

        <!-- CHI TIẾT HÀNG HÓA -->
        <div class="p-8">
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-tighter flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                Danh sách thuốc nhập
            </h3>

            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider text-[10px]">STT</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider text-[10px]">Sản phẩm</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider text-[10px] text-center">SL Sỉ Nhập</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider text-[10px] text-right">Đơn giá Sỉ</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider text-[10px] text-center bg-blue-50/50 border-x border-blue-100">Kho thực nhận</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider text-[10px] text-right">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($phieuNhap->chiTiet as $index => $item)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 font-medium">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-800">{{ $item->thuoc->ten_thuoc ?? 'Sản phẩm đã bị xóa' }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">HSD Lô: {{ $item->han_su_dung_moi ? $item->han_su_dung_moi->format('d/m/Y') : 'N/A' }}</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="font-black text-slate-700">{{ $item->so_luong_nhap }}</span> 
                                    <span class="text-xs text-slate-500">{{ $item->don_vi_nhap }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="font-medium">{{ number_format($item->gia_nhap, 0, ',', '.') }}₫</span>
                                </td>
                                <!-- Khu vực quy đổi rõ ràng -->
                                <td class="px-4 py-3 text-center bg-blue-50/30 border-x border-blue-50">
                                    <div class="flex flex-col items-center">
                                        <span class="font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded text-sm">+{{ $item->so_luong_co_ban }} {{ $item->thuoc->don_vi_co_ban ?? 'Lẻ' }}</span>
                                        <span class="text-[9px] text-slate-400 mt-1">(Tỷ lệ 1:{{ $item->ty_le_quy_doi }})</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right font-black text-slate-800">
                                    {{ number_format($item->thanh_tien, 0, ',', '.') }}₫
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- TỔNG KẾT PHIẾU -->
            <div class="mt-8 flex justify-end">
                <div class="w-80 bg-slate-50 p-6 rounded-2xl border border-slate-200">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-slate-500 font-medium text-sm">Cộng tiền hàng:</span>
                        <span class="text-slate-700 font-bold">{{ number_format($phieuNhap->tong_tien_nhap, 0, ',', '.') }}₫</span>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-slate-200">
                        <span class="text-slate-800 font-black uppercase text-sm">Tổng cộng:</span>
                        <span class="text-2xl font-black text-blue-600">{{ number_format($phieuNhap->tong_tien_nhap, 0, ',', '.') }}₫</span>
                    </div>
                </div>
            </div>

            <div class="mt-10 grid grid-cols-2 text-center text-sm print:block">
                <div>
                    <p class="font-bold text-slate-800 mb-16">Người giao hàng</p>
                    <p class="text-slate-400 italic">(Ký, ghi rõ họ tên)</p>
                </div>
                <div>
                    <p class="font-bold text-slate-800 mb-16">Kế toán / Người lập phiếu</p>
                    <p class="text-slate-800 font-bold">{{ $phieuNhap->nguoiLap->ho_ten ?? '' }}</p>
                </div>
            </div>

        </div>
    </div>
@endsection