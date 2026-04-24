@extends('admin.layout')

@section('title', 'Chi tiết Hóa Đơn - Admin')
@section('page_title', 'Chi Tiết Hóa Đơn #ORD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT))

@section('content')
    <!-- THANH CÔNG CỤ (Sẽ bị ẩn khi in nhờ class print:hidden) -->
    <div class="mb-6 flex flex-wrap gap-4 justify-between items-center print:hidden">
        <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 font-bold text-sm rounded-xl hover:bg-slate-50 transition flex items-center gap-2 no-underline shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại danh sách
        </a>

        <div class="flex gap-2">
            <button onclick="window.print()" class="px-5 py-2 bg-indigo-600 text-white font-bold text-sm rounded-xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                In Hóa Đơn
            </button>
        </div>
    </div>

    <!-- KHUNG IN HÓA ĐƠN CHÍNH -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-4xl mx-auto print:shadow-none print:border-none print:m-0 print:rounded-none">
        
        <!-- Header Hóa Đơn -->
        <div class="p-8 border-b border-slate-100 flex justify-between items-start print:p-0 print:pb-4 print:border-black">
            <div>
                <h1 class="text-3xl font-black text-blue-600 tracking-tight mb-2 print:text-black">DYLY PHARMA</h1>
                <p class="text-slate-500 text-sm print:text-black">Địa chỉ: 123 Đường Y Tế, Phường Khỏe Mạnh, TP.HCM</p>
                <p class="text-slate-500 text-sm print:text-black">Hotline: 1900 9999</p>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-bold text-slate-800 uppercase tracking-widest mb-1 print:text-black">Hóa Đơn Bán Lẻ</h2>
                <p class="text-slate-500 font-medium print:text-black">Mã Đơn: <span class="text-slate-800 font-bold">#ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span></p>
                <p class="text-slate-500 font-medium print:text-black">Ngày lập: <span class="text-slate-800">{{ $order->created_at->format('d/m/Y H:i') }}</span></p>
            </div>
        </div>

        <!-- Thông tin Phụ -->
        <div class="px-8 py-6 bg-slate-50/50 flex justify-between border-b border-slate-100 print:bg-white print:p-0 print:py-4 print:border-none">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1 print:text-black">Trạng thái thanh toán</p>
                @if($order->status == 'completed')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-700 print:border print:border-black print:bg-transparent print:text-black">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 print:hidden"></span> ĐÃ THANH TOÁN
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-700 print:border print:border-black print:bg-transparent print:text-black">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 print:hidden"></span> CHƯA THANH TOÁN
                    </span>
                @endif
            </div>
            <div class="text-right">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1 print:text-black">Nhân viên phụ trách</p>
                <p class="font-bold text-slate-800 print:text-black">{{ $order->nhanVien->ho_ten ?? 'Hệ thống' }}</p>
            </div>
        </div>

        <!-- Bảng Chi tiết -->
        <div class="p-8 print:p-0 print:pt-4">
            <table class="w-full text-left text-sm text-slate-600 mb-8 print:text-black">
                <thead class="border-b-2 border-slate-200 text-slate-800 print:border-black">
                    <tr>
                        <th class="py-3 font-bold">STT</th>
                        <th class="py-3 font-bold">Tên sản phẩm (Thuốc)</th>
                        <th class="py-3 font-bold text-center">ĐVT</th>
                        <th class="py-3 font-bold text-right">Đơn giá</th>
                        <th class="py-3 font-bold text-center">SL</th>
                        <th class="py-3 font-bold text-right">Thành tiền</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 print:divide-black/20">
                    @forelse($order->items as $index => $item)
                        <tr>
                            <td class="py-4">{{ $index + 1 }}</td>
                            <td class="py-4 font-semibold text-slate-800 print:text-black">
                                {{ $item->thuoc->ten_thuoc ?? 'Sản phẩm đã bị xóa khỏi hệ thống' }}
                            </td>
                            <td class="py-4 text-center">{{ $item->thuoc->don_vi_tinh ?? '-' }}</td>
                            <td class="py-4 text-right">{{ number_format($item->price, 0, ',', '.') }}₫</td>
                            <td class="py-4 text-center font-bold">{{ $item->quantity }}</td>
                            <td class="py-4 text-right font-bold text-slate-800 print:text-black">{{ number_format($item->thanh_tien, 0, ',', '.') }}₫</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center italic text-slate-400">Không có chi tiết sản phẩm.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Tổng kết -->
            <div class="flex justify-end print:mt-4">
                <div class="w-72 bg-slate-50 p-6 rounded-2xl border border-slate-100 print:bg-white print:border-black print:rounded-none print:p-4">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-slate-500 font-medium text-sm print:text-black">Tạm tính:</span>
                        <span class="text-slate-700 font-bold print:text-black">{{ number_format($order->total_price, 0, ',', '.') }}₫</span>
                    </div>
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-slate-500 font-medium text-sm print:text-black">Giảm giá:</span>
                        <span class="text-slate-700 font-bold print:text-black">0₫</span>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-slate-200 print:border-black">
                        <span class="text-slate-800 font-black uppercase text-sm print:text-black">Tổng cộng:</span>
                        <span class="text-2xl font-black text-blue-600 print:text-black">{{ number_format($order->total_price, 0, ',', '.') }}₫</span>
                    </div>
                </div>
            </div>

            <!-- Ghi chú -->
            @if($order->note)
            <div class="mt-8 pt-6 border-t border-slate-100 print:border-black">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2 print:text-black">Ghi chú đơn hàng:</p>
                <p class="text-slate-600 text-sm italic border-l-4 border-blue-500 pl-3 py-1 print:border-black print:text-black">{{ $order->note }}</p>
            </div>
            @endif

            <!-- Lời cảm ơn (Chỉ hiển thị khi in) -->
            <div class="mt-12 text-center text-slate-500 text-xs hidden print:block print:text-black">
                <p class="mb-1 font-bold">Cảm ơn quý khách đã mua sắm tại DYLY Pharma!</p>
                <p>Hàng mua rồi miễn đổi trả trừ trường hợp lỗi từ nhà sản xuất trong vòng 3 ngày.</p>
            </div>
        </div>
    </div>

    <!-- Script tự động mở hộp thoại in nếu trên URL có tham số ?print=true -->
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('print')) {
                // Đợi 0.5s cho CSS (Tailwind) tải xong để bản in không bị vỡ Layout
                setTimeout(() => {
                    window.print();
                }, 500);
            }
        });
    </script>
@endsection