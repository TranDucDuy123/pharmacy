@extends('admin.layout')

@section('title', 'Tạo Phiếu Nhập Kho')
@section('page_title', 'Tạo Phiếu Nhập Kho')

@section('content')
<div x-data="phieuNhapForm()" class="max-w-7xl mx-auto pb-10">
    
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('phieu-nhap.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 font-bold text-sm rounded-xl hover:bg-slate-50 transition flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Hủy / Quay lại
        </a>
        
        <div class="text-right">
            <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest block">Tổng thanh toán</span>
            <span class="text-3xl font-black text-blue-600" x-text="new Intl.NumberFormat('vi-VN').format(calculateTotal()) + '₫'">0₫</span>
        </div>
    </div>

    <!-- Thông tin chung -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Nhà cung cấp <span class="text-red-500">*</span></label>
            <select x-model="nhaCungCapId" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition font-bold text-slate-800">
                <option value="">-- Chọn Nhà Cung Cấp --</option>
                @foreach($nhaCungCaps as $ncc)
                    <option value="{{ $ncc->id }}">{{ $ncc->ten_ncc }} ({{ $ncc->so_dien_thoai ?? 'Không có SĐT' }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Ghi chú phiếu nhập</label>
            <input type="text" x-model="ghiChu" placeholder="Số hóa đơn, người giao..." class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition text-slate-800">
        </div>
    </div>

    <!-- Danh sách Thuốc nhập -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-tighter flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                Chi tiết hàng nhập
            </h3>
            <button @click="addItem()" class="px-4 py-2 bg-indigo-50 text-indigo-700 text-xs font-black rounded-lg hover:bg-indigo-100 transition shadow-sm uppercase tracking-widest">
                + Thêm dòng
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/80 text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-semibold uppercase text-[10px] w-64">Chọn Thuốc</th>
                        <th class="px-4 py-3 font-semibold uppercase text-[10px] w-32">Đơn vị sỉ</th>
                        <th class="px-4 py-3 font-semibold uppercase text-[10px] w-24">SL Sỉ</th>
                        <th class="px-4 py-3 font-semibold uppercase text-[10px] w-36">Giá nhập (1 Đ.vị sỉ)</th>
                        <th class="px-4 py-3 font-semibold uppercase text-[10px] w-48 text-center bg-blue-50/50">Kho sẽ cộng (Quy đổi)</th>
                        <th class="px-4 py-3 font-semibold uppercase text-[10px] w-32">HSD Lô này</th>
                        <th class="px-4 py-3 font-semibold uppercase text-[10px] text-right">Thành tiền</th>
                        <th class="px-4 py-3 w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="(item, index) in items" :key="index">
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            
                            <!-- CHỌN THUỐC -->
                            <td class="px-4 py-3">
                                <select x-model="item.thuoc_id" @change="onThuocChange(index)" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 outline-none font-bold text-slate-700">
                                    <option value="">-- Chọn thuốc --</option>
                                    <template x-for="thuoc in thuocsList" :key="thuoc.id">
                                        <option :value="thuoc.id" x-text="thuoc.ten_thuoc + ' (' + thuoc.ma_thuoc + ')'"></option>
                                    </template>
                                </select>
                            </td>

                            <!-- ĐƠN VỊ SỈ -->
                            <td class="px-4 py-3">
                                <input type="text" x-model="item.don_vi_nhap" readonly class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-lg text-xs font-bold text-slate-500 cursor-not-allowed">
                            </td>

                            <!-- SỐ LƯỢNG SỈ -->
                            <td class="px-4 py-3">
                                <input type="number" x-model.number="item.so_luong_nhap" min="1" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 outline-none font-black text-blue-700 text-center">
                            </td>

                            <!-- GIÁ NHẬP SỈ -->
                            <td class="px-4 py-3">
                                <div class="relative">
                                    <input type="number" x-model.number="item.gia_nhap" min="0" class="w-full pl-3 pr-8 py-2 bg-white border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 outline-none font-bold text-slate-700">
                                    <span class="absolute right-3 top-2 text-[10px] text-slate-400">₫</span>
                                </div>
                            </td>

                            <!-- KHU VỰC TỰ ĐỘNG QUY ĐỔI (TRÁI TIM LOGIC) -->
                            <td class="px-4 py-3 text-center bg-blue-50/30">
                                <template x-if="item.thuoc_id">
                                    <div class="flex flex-col items-center">
                                        <span class="text-[10px] text-slate-400 font-medium">Tỷ lệ: 1 <span x-text="item.don_vi_nhap"></span> = <span x-text="item.ty_le_quy_doi"></span> <span x-text="item.don_vi_co_ban"></span></span>
                                        <span class="font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded text-sm mt-1">
                                            +<span x-text="(item.so_luong_nhap || 0) * (item.ty_le_quy_doi || 1)"></span> <span class="text-[10px]" x-text="item.don_vi_co_ban"></span>
                                        </span>
                                    </div>
                                </template>
                            </td>

                            <!-- HSD -->
                            <td class="px-4 py-3">
                                <input type="date" x-model="item.han_su_dung" class="w-full px-2 py-2 bg-white border border-slate-200 rounded-lg text-[11px] focus:ring-2 focus:ring-blue-500 outline-none font-medium">
                            </td>

                            <!-- THÀNH TIỀN -->
                            <td class="px-4 py-3 text-right">
                                <span class="font-black text-slate-800" x-text="new Intl.NumberFormat('vi-VN').format((item.so_luong_nhap || 0) * (item.gia_nhap || 0)) + '₫'"></span>
                            </td>

                            <!-- XÓA DÒNG -->
                            <td class="px-4 py-3 text-center">
                                <button @click="removeItem(index)" class="text-red-400 hover:text-red-600 transition p-1 bg-red-50 rounded-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </td>

                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        
        <div x-show="items.length === 0" class="p-8 text-center text-slate-400 italic text-sm">
            Chưa có mặt hàng nào. Vui lòng bấm "+ Thêm dòng".
        </div>
    </div>

    <!-- Nút Submit -->
    <div class="flex justify-end">
        <button @click="submitForm()" :disabled="isSubmitting || items.length === 0 || !nhaCungCapId" 
                class="px-8 py-4 bg-blue-600 text-white font-black rounded-2xl shadow-xl shadow-blue-200 hover:bg-blue-700 hover:-translate-y-1 active:scale-95 transition-all uppercase tracking-widest text-sm flex items-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed">
            <span x-show="!isSubmitting">Hoàn Tất & Lưu Kho</span>
            <span x-show="isSubmitting">Đang xử lý...</span>
        </button>
    </div>
</div>

<!-- Kịch bản AlpineJS -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('phieuNhapForm', () => ({
            thuocsList: @json($thuocs), // Dữ liệu thuốc từ Backend ném sang
            nhaCungCapId: '',
            ghiChu: '',
            items: [],
            isSubmitting: false,

            init() {
                this.addItem(); // Mở lên có sẵn 1 dòng
            },

            addItem() {
                this.items.push({
                    thuoc_id: '',
                    don_vi_nhap: '',
                    don_vi_co_ban: '',
                    ty_le_quy_doi: 1,
                    so_luong_nhap: 1,
                    gia_nhap: 0,
                    han_su_dung: '',
                });
            },

            removeItem(index) {
                this.items.splice(index, 1);
            },

            // Hàm tự động điền dữ liệu khi chọn Thuốc
            onThuocChange(index) {
                let id = this.items[index].thuoc_id;
                let thuoc = this.thuocsList.find(t => t.id == id);
                
                if(thuoc) {
                    this.items[index].don_vi_nhap = thuoc.don_vi_nhap || thuoc.don_vi_co_ban || 'Hộp';
                    this.items[index].don_vi_co_ban = thuoc.don_vi_co_ban || 'Viên';
                    this.items[index].ty_le_quy_doi = thuoc.ty_le_quy_doi || 1;
                    
                    // Giá nhập lấy từ DB lưu theo Đơn vị cơ bản (Lẻ). 
                    // Nên giá nhập 1 Sỉ = Giá nhập lẻ * Tỷ lệ.
                    let giaNhapLe = thuoc.gia_nhap || 0;
                    this.items[index].gia_nhap = giaNhapLe * this.items[index].ty_le_quy_doi;
                    
                    // Format Date YYYY-MM-DD nếu HSD tồn tại
                    if(thuoc.han_su_dung) {
                        this.items[index].han_su_dung = thuoc.han_su_dung.substring(0, 10); 
                    }
                } else {
                    // Reset nếu bỏ chọn
                    this.items[index].don_vi_nhap = '';
                    this.items[index].ty_le_quy_doi = 1;
                    this.items[index].gia_nhap = 0;
                }
            },

            calculateTotal() {
                return this.items.reduce((total, item) => {
                    let sl = parseInt(item.so_luong_nhap) || 0;
                    let gia = parseInt(item.gia_nhap) || 0;
                    return total + (sl * gia);
                }, 0);
            },

            async submitForm() {
                // Validate sương sương
                let invalidItem = this.items.find(i => !i.thuoc_id || !i.so_luong_nhap || !i.gia_nhap || !i.han_su_dung);
                if (invalidItem) {
                    alert("Vui lòng điền đầy đủ (Thuốc, Số lượng, Giá nhập, HSD) cho tất cả các dòng!");
                    return;
                }

                if(!confirm(`Xác nhận nhập kho với tổng tiền ${new Intl.NumberFormat('vi-VN').format(this.calculateTotal())}₫?`)) return;

                this.isSubmitting = true;

                try {
                    const response = await fetch('{{ route('phieu-nhap.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            nha_cung_cap_id: this.nhaCungCapId,
                            ghi_chu: this.ghiChu,
                            items: this.items
                        })
                    });

                    const result = await response.json();

                    if(response.ok && result.success) {
                        alert(result.message);
                        window.location.href = result.redirect;
                    } else {
                        alert("Lỗi: " + (result.message || "Kiểm tra lại dữ liệu"));
                        this.isSubmitting = false;
                    }
                } catch (error) {
                    alert("Lỗi kết nối máy chủ!");
                    this.isSubmitting = false;
                }
            }
        }));
    });
</script>
@endsection