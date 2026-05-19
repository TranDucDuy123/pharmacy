@extends('admin.layout')

@section('title', 'Tạo Phiếu Nhập - Admin')
@section('page_title', 'Tạo Phiếu Nhập')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('phieu-nhap.index') }}"
           class="px-4 py-2 bg-white border border-slate-200 text-slate-600 font-bold text-sm rounded-xl hover:bg-slate-50 transition flex items-center gap-2 no-underline shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Quay lại phiếu nhập
        </a>
    </div>

    <div x-data="phieuNhapCreate(@js($thuocs))" x-init="init()">
        <template x-if="errorMessage">
            <div class="mb-6 px-6 py-4 bg-red-50 border border-red-200 text-red-600 rounded-2xl shadow-sm">
                <div class="flex items-center gap-3 mb-2">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                              clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-black uppercase text-sm tracking-widest">Dữ liệu không hợp lệ</span>
                </div>
                <p class="text-sm font-medium ml-9" x-text="errorMessage"></p>
            </div>
        </template>

        <template x-if="successMessage">
            <div class="mb-6 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium text-sm" x-text="successMessage"></span>
                </div>
            </div>
        </template>

        <form @submit.prevent="submitForm" class="max-w-7xl space-y-8">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">

                    {{-- THÔNG TIN PHIẾU --}}
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-base font-black text-slate-800 uppercase tracking-tighter">
                                Thông tin phiếu nhập
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Nhà cung cấp <span class="text-red-500">*</span>
                                </label>
                                <select x-model="form.nha_cung_cap_id"
                                        required
                                        class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition font-bold text-slate-700">
                                    <option value="">-- Chọn nhà cung cấp --</option>
                                    @foreach($nhaCungCaps as $ncc)
                                        <option value="{{ $ncc->id }}">
                                            {{ $ncc->ten_ncc }} - {{ $ncc->ma_ncc }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Trạng thái xử lý
                                </label>
                                <div class="px-5 py-3.5 bg-emerald-50 border border-emerald-100 rounded-2xl text-emerald-600 font-black text-sm">
                                    Tạo phiếu & cộng tồn kho ngay
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Ghi chú
                                </label>
                                <textarea x-model="form.ghi_chu"
                                          rows="3"
                                          placeholder="VD: Nhập hàng định kỳ, nhập bổ sung thuốc bán chạy..."
                                          class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition text-slate-800"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- DANH SÁCH THUỐC NHẬP --}}
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
                        <div class="flex items-center justify-between gap-3 mb-8">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M20 13V7a2 2 0 00-2-2h-4l-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2h7"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-black text-slate-800 uppercase tracking-tighter">
                                        Thuốc cần nhập
                                    </h3>
                                    <p class="text-xs text-slate-400 mt-1">
                                        Nhập theo đơn vị nhập, hệ thống tự quy đổi sang tồn kho cơ bản.
                                    </p>
                                </div>
                            </div>

                            <button type="button"
                                    @click="addItem"
                                    class="px-4 py-2.5 bg-blue-600 text-white font-black text-xs rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-100">
                                + Thêm thuốc
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-slate-50 text-slate-500">
                                    <tr>
                                        <th class="px-4 py-3 font-black uppercase text-[10px] min-w-[280px]">Thuốc</th>
                                        <th class="px-4 py-3 font-black uppercase text-[10px] min-w-[170px]">Quy đổi</th>
                                        <th class="px-4 py-3 font-black uppercase text-[10px] text-center min-w-[130px]">SL nhập</th>
                                        <th class="px-4 py-3 font-black uppercase text-[10px] text-right min-w-[160px]">Giá nhập</th>
                                        <th class="px-4 py-3 font-black uppercase text-[10px] text-center min-w-[160px]">Hạn sử dụng</th>
                                        <th class="px-4 py-3 font-black uppercase text-[10px] text-right min-w-[150px]">Thành tiền</th>
                                        <th class="px-4 py-3 font-black uppercase text-[10px] text-center w-16">Xóa</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-100">
                                    <template x-for="(item, index) in form.items" :key="item.key">
                                        <tr class="align-top">
                                            <td class="px-4 py-4">
                                                <select x-model="item.thuoc_id"
                                                        @change="selectThuoc(item)"
                                                        required
                                                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-blue-500">
                                                    <option value="">-- Chọn thuốc --</option>
                                                    <template x-for="thuoc in thuocs" :key="thuoc.id">
                                                        <option :value="thuoc.id"
                                                                :disabled="isThuocSelected(thuoc.id, item)"
                                                                x-text="thuoc.ten_thuoc + ' - ' + thuoc.ma_thuoc"></option>
                                                    </template>
                                                </select>

                                                <div class="mt-2 text-[11px] text-slate-400" x-show="item.ten_thuoc">
                                                    <p>
                                                        Mã:
                                                        <b x-text="item.ma_thuoc"></b>
                                                    </p>
                                                    <p>
                                                        Đơn vị bán lẻ:
                                                        <b x-text="item.don_vi_co_ban"></b>
                                                    </p>
                                                </div>
                                            </td>

                                            <td class="px-4 py-4">
                                                <div x-show="item.thuoc_id"
                                                     class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-2 rounded-xl">
                                                    1 <span x-text="item.don_vi_nhap"></span>
                                                    =
                                                    <span x-text="item.ty_le_quy_doi"></span>
                                                    <span x-text="item.don_vi_co_ban"></span>
                                                </div>

                                                <div x-show="!item.thuoc_id"
                                                     class="text-xs text-slate-400 italic">
                                                    Chọn thuốc trước
                                                </div>
                                            </td>

                                            <td class="px-4 py-4">
                                                <input type="number"
                                                       x-model.number="item.so_luong_nhap"
                                                       min="1"
                                                       required
                                                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-center font-black outline-none focus:ring-2 focus:ring-blue-500">

                                                <p class="mt-2 text-[10px] text-slate-400 text-center" x-show="item.thuoc_id">
                                                    Cộng kho:
                                                    <b class="text-emerald-600"
                                                       x-text="formatNumber(item.so_luong_nhap * item.ty_le_quy_doi)"></b>
                                                    <span x-text="item.don_vi_co_ban"></span>
                                                </p>
                                            </td>

                                            <td class="px-4 py-4">
                                                <div class="relative">
                                                    <input type="number"
                                                           x-model.number="item.gia_nhap"
                                                           min="0"
                                                           step="100"
                                                           required
                                                           class="w-full pl-4 pr-9 py-3 bg-slate-50 border border-slate-200 rounded-xl text-right font-bold outline-none focus:ring-2 focus:ring-blue-500">
                                                    <span class="absolute right-3 top-3 text-slate-400 font-bold text-sm">₫</span>
                                                </div>

                                                <p class="mt-2 text-[10px] text-slate-400 text-right" x-show="item.thuoc_id">
                                                    Giá / <span x-text="item.don_vi_nhap"></span>
                                                </p>
                                            </td>

                                            <td class="px-4 py-4">
                                                <input type="date"
                                                       x-model="item.han_su_dung"
                                                       required
                                                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-blue-500">

                                                <p class="mt-2 text-[10px] text-slate-400 text-center" x-show="item.han_su_dung">
                                                    HSD mới
                                                </p>
                                            </td>

                                            <td class="px-4 py-4 text-right">
                                                <span class="font-black text-blue-600"
                                                      x-text="formatPrice(item.so_luong_nhap * item.gia_nhap)"></span>
                                            </td>

                                            <td class="px-4 py-4 text-center">
                                                <button type="button"
                                                        @click="removeItem(index)"
                                                        :disabled="form.items.length === 1"
                                                        class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition disabled:opacity-30 disabled:cursor-not-allowed">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- TỔNG KẾT --}}
                <div class="space-y-6">
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 sticky top-6">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2"></path>
                                </svg>
                            </div>
                            <h3 class="text-base font-black text-slate-800 uppercase tracking-tighter">Tổng kết nhập kho</h3>
                        </div>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-4 bg-slate-50 rounded-2xl">
                                <span class="text-xs font-black text-slate-400 uppercase">Số loại thuốc</span>
                                <span class="font-black text-slate-800" x-text="form.items.length"></span>
                            </div>

                            <div class="flex justify-between items-center p-4 bg-slate-50 rounded-2xl">
                                <span class="text-xs font-black text-slate-400 uppercase">Tổng SL nhập</span>
                                <span class="font-black text-slate-800" x-text="formatNumber(tongSoLuongNhap)"></span>
                            </div>

                            <div class="flex justify-between items-center p-4 bg-slate-50 rounded-2xl">
                                <span class="text-xs font-black text-slate-400 uppercase">Tổng SL cộng kho</span>
                                <span class="font-black text-emerald-600" x-text="formatNumber(tongSoLuongCoBan)"></span>
                            </div>

                            <div class="p-5 bg-blue-50 rounded-2xl border border-blue-100">
                                <div class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-2">
                                    Tổng tiền nhập
                                </div>
                                <div class="text-3xl font-black text-blue-700" x-text="formatPrice(tongTien)"></div>
                            </div>
                        </div>

                        <button type="submit"
                                :disabled="isSubmitting"
                                class="mt-8 w-full py-5 bg-blue-600 text-white font-black rounded-3xl shadow-xl shadow-blue-200 hover:bg-blue-700 hover:-translate-y-1 active:scale-95 transition-all uppercase tracking-widest text-sm disabled:opacity-60 disabled:cursor-not-allowed">
                            <span x-show="!isSubmitting">Xác nhận nhập kho</span>
                            <span x-show="isSubmitting">Đang xử lý...</span>
                        </button>

                        <p class="mt-4 text-[10px] text-center text-slate-400 font-medium px-4">
                            Khi xác nhận, hệ thống sẽ tạo phiếu nhập, lưu chi tiết thuốc nhập, cộng tồn kho và cập nhật giá nhập / hạn sử dụng mới.
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- <script>
        function phieuNhapCreate(thuocs) {
            return {
                thuocs: thuocs,
                isSubmitting: false,
                errorMessage: '',
                successMessage: '',

                form: {
                    nha_cung_cap_id: '',
                    ghi_chu: '',
                    items: []
                },

                init() {
                    this.addItem();
                },

                addItem() {
                    this.form.items.push({
                        key: Date.now() + Math.random(),
                        thuoc_id: '',
                        ma_thuoc: '',
                        ten_thuoc: '',
                        don_vi_nhap: '',
                        don_vi_co_ban: '',
                        ty_le_quy_doi: 1,
                        so_luong_nhap: 1,
                        gia_nhap: 0,
                        han_su_dung: ''
                    });
                },

                removeItem(index) {
                    if (this.form.items.length === 1) {
                        return;
                    }

                    this.form.items.splice(index, 1);
                },

                selectThuoc(item) {
                    const thuoc = this.thuocs.find(t => Number(t.id) === Number(item.thuoc_id));

                    if (!thuoc) {
                        item.ma_thuoc = '';
                        item.ten_thuoc = '';
                        item.don_vi_nhap = '';
                        item.don_vi_co_ban = '';
                        item.ty_le_quy_doi = 1;
                        item.gia_nhap = 0;
                        item.han_su_dung = '';
                        return;
                    }

                    const tyLe = Number(thuoc.ty_le_quy_doi || 1);

                    item.ma_thuoc = thuoc.ma_thuoc || '';
                    item.ten_thuoc = thuoc.ten_thuoc || '';
                    item.don_vi_co_ban = thuoc.don_vi_co_ban || 'Đơn vị';
                    item.don_vi_nhap = thuoc.don_vi_nhap || thuoc.don_vi_co_ban || 'Đơn vị';
                    item.ty_le_quy_doi = tyLe;

                    // Trong bảng thuốc, gia_nhap đang là giá theo đơn vị cơ bản.
                    // Controller phiếu nhập đang nhận gia_nhap theo đơn vị nhập.
                    item.gia_nhap = Number(thuoc.gia_nhap || 0) * tyLe;

                    // Gợi ý HSD hiện tại nếu có, nhân viên vẫn có thể sửa thành HSD lô mới.
                    item.han_su_dung = thuoc.han_su_dung
                        ? String(thuoc.han_su_dung).substring(0, 10)
                        : '';
                },

                isThuocSelected(thuocId, currentItem) {
                    return this.form.items.some(item => {
                        return item !== currentItem && Number(item.thuoc_id) === Number(thuocId);
                    });
                },

                get tongTien() {
                    return this.form.items.reduce((total, item) => {
                        return total + (Number(item.so_luong_nhap || 0) * Number(item.gia_nhap || 0));
                    }, 0);
                },

                get tongSoLuongNhap() {
                    return this.form.items.reduce((total, item) => {
                        return total + Number(item.so_luong_nhap || 0);
                    }, 0);
                },

                get tongSoLuongCoBan() {
                    return this.form.items.reduce((total, item) => {
                        return total + (Number(item.so_luong_nhap || 0) * Number(item.ty_le_quy_doi || 1));
                    }, 0);
                },

                validateForm() {
                    if (!this.form.nha_cung_cap_id) {
                        return 'Vui lòng chọn nhà cung cấp.';
                    }

                    if (!this.form.items.length) {
                        return 'Vui lòng thêm ít nhất một thuốc vào phiếu nhập.';
                    }

                    const usedThuocIds = [];

                    for (const item of this.form.items) {
                        if (!item.thuoc_id) {
                            return 'Vui lòng chọn thuốc cho tất cả các dòng.';
                        }

                        if (usedThuocIds.includes(Number(item.thuoc_id))) {
                            return 'Một thuốc không nên nhập lặp lại nhiều dòng. Vui lòng kiểm tra lại.';
                        }

                        usedThuocIds.push(Number(item.thuoc_id));

                        if (!item.so_luong_nhap || Number(item.so_luong_nhap) < 1) {
                            return 'Số lượng nhập phải lớn hơn hoặc bằng 1.';
                        }

                        if (item.gia_nhap === '' || Number(item.gia_nhap) < 0) {
                            return 'Giá nhập không hợp lệ.';
                        }

                        if (!item.han_su_dung) {
                            return 'Vui lòng nhập hạn sử dụng cho tất cả thuốc.';
                        }
                    }

                    return '';
                },

                buildPayload() {
                    return {
                        nha_cung_cap_id: this.form.nha_cung_cap_id,
                        ghi_chu: this.form.ghi_chu,
                        items: this.form.items.map(item => ({
                            thuoc_id: item.thuoc_id,
                            don_vi_nhap: item.don_vi_nhap,
                            so_luong_nhap: item.so_luong_nhap,
                            gia_nhap: item.gia_nhap,
                            ty_le_quy_doi: item.ty_le_quy_doi,
                            han_su_dung: item.han_su_dung
                        }))
                    };
                },

                async submitForm() {
                    this.errorMessage = '';
                    this.successMessage = '';

                    const validationError = this.validateForm();

                    if (validationError) {
                        this.errorMessage = validationError;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        return;
                    }

                    if (!confirm('Xác nhận lập phiếu nhập và cộng tồn kho ngay?')) {
                        return;
                    }

                    this.isSubmitting = true;

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                        const response = await fetch('{{ route('phieu-nhap.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify(this.buildPayload())
                        });

                        const result = await response.json();

                        if (response.ok && result.success) {
                            this.successMessage = result.message || 'Lập phiếu nhập thành công!';
                            window.scrollTo({ top: 0, behavior: 'smooth' });

                            setTimeout(() => {
                                window.location.href = result.redirect || '{{ route('phieu-nhap.index') }}';
                            }, 700);

                            return;
                        }

                        if (result.errors) {
                            this.errorMessage = Object.values(result.errors).flat().join(' ');
                        } else {
                            this.errorMessage = result.message || 'Không thể lập phiếu nhập.';
                        }

                        window.scrollTo({ top: 0, behavior: 'smooth' });

                    } catch (error) {
                        console.error(error);
                        this.errorMessage = 'Không thể kết nối đến máy chủ. Vui lòng thử lại.';
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                formatPrice(price) {
                    return new Intl.NumberFormat('vi-VN').format(price || 0) + '₫';
                },

                formatNumber(number) {
                    return new Intl.NumberFormat('vi-VN').format(number || 0);
                }
            }
        }
    </script> --}}
    <script>
        function phieuNhapCreate(thuocs) {
            return {
                thuocs: thuocs || [],
                isSubmitting: false,
                errorMessage: '',
                successMessage: '',

                form: {
                    nha_cung_cap_id: '',
                    ghi_chu: '',
                    items: []
                },

                init() {
                    if (this.form.items.length === 0) {
                        this.addItem();
                    }

                    console.log('Thuốc truyền vào create:', this.thuocs);
                },

                addItem() {
                    this.form.items.push({
                        key: Date.now() + Math.random(),
                        thuoc_id: '',
                        ma_thuoc: '',
                        ten_thuoc: '',
                        don_vi_nhap: '',
                        don_vi_co_ban: '',
                        ty_le_quy_doi: 1,
                        so_luong_nhap: 1,
                        gia_nhap: 0,
                        han_su_dung: ''
                    });
                },

                removeItem(index) {
                    if (this.form.items.length === 1) {
                        return;
                    }

                    this.form.items.splice(index, 1);
                },

                selectThuoc(item) {
                    const thuoc = this.thuocs.find(t => Number(t.id) === Number(item.thuoc_id));

                    if (!thuoc) {
                        item.ma_thuoc = '';
                        item.ten_thuoc = '';
                        item.don_vi_nhap = '';
                        item.don_vi_co_ban = '';
                        item.ty_le_quy_doi = 1;
                        item.gia_nhap = 0;
                        item.han_su_dung = '';
                        return;
                    }

                    const tyLe = Number(thuoc.ty_le_quy_doi || 1);

                    item.ma_thuoc = thuoc.ma_thuoc || '';
                    item.ten_thuoc = thuoc.ten_thuoc || '';
                    item.don_vi_co_ban = thuoc.don_vi_co_ban || thuoc.don_vi_tinh || 'Đơn vị';
                    item.don_vi_nhap = thuoc.don_vi_nhap || item.don_vi_co_ban;
                    item.ty_le_quy_doi = tyLe;

                    // Thuoc.gia_nhap đang là giá theo đơn vị cơ bản.
                    // Controller phiếu nhập đang nhận gia_nhap theo đơn vị nhập.
                    item.gia_nhap = Number(thuoc.gia_nhap || 0) * tyLe;

                    item.han_su_dung = thuoc.han_su_dung
                        ? String(thuoc.han_su_dung).substring(0, 10)
                        : '';
                },

                isThuocSelected(thuocId, currentItem) {
                    return this.form.items.some(item => {
                        return item !== currentItem && Number(item.thuoc_id) === Number(thuocId);
                    });
                },

                get tongTien() {
                    return this.form.items.reduce((total, item) => {
                        return total + (Number(item.so_luong_nhap || 0) * Number(item.gia_nhap || 0));
                    }, 0);
                },

                get tongSoLuongNhap() {
                    return this.form.items.reduce((total, item) => {
                        return total + Number(item.so_luong_nhap || 0);
                    }, 0);
                },

                get tongSoLuongCoBan() {
                    return this.form.items.reduce((total, item) => {
                        return total + (Number(item.so_luong_nhap || 0) * Number(item.ty_le_quy_doi || 1));
                    }, 0);
                },

                validateForm() {
                    if (!this.form.nha_cung_cap_id) {
                        return 'Vui lòng chọn nhà cung cấp.';
                    }

                    if (!this.form.items.length) {
                        return 'Vui lòng thêm ít nhất một thuốc vào phiếu nhập.';
                    }

                    const usedThuocIds = [];

                    for (const item of this.form.items) {
                        if (!item.thuoc_id) {
                            return 'Vui lòng chọn thuốc cho tất cả các dòng.';
                        }

                        if (usedThuocIds.includes(Number(item.thuoc_id))) {
                            return 'Một thuốc không nên nhập lặp lại nhiều dòng. Vui lòng kiểm tra lại.';
                        }

                        usedThuocIds.push(Number(item.thuoc_id));

                        if (!item.so_luong_nhap || Number(item.so_luong_nhap) < 1) {
                            return 'Số lượng nhập phải lớn hơn hoặc bằng 1.';
                        }

                        if (item.gia_nhap === '' || Number(item.gia_nhap) < 0) {
                            return 'Giá nhập không hợp lệ.';
                        }

                        if (!item.han_su_dung) {
                            return 'Vui lòng nhập hạn sử dụng cho tất cả thuốc.';
                        }
                    }

                    return '';
                },

                buildPayload() {
                    return {
                        nha_cung_cap_id: Number(this.form.nha_cung_cap_id),
                        ghi_chu: this.form.ghi_chu,
                        items: this.form.items.map(item => ({
                            thuoc_id: Number(item.thuoc_id),
                            don_vi_nhap: item.don_vi_nhap,
                            so_luong_nhap: Number(item.so_luong_nhap),
                            gia_nhap: Number(item.gia_nhap),
                            ty_le_quy_doi: Number(item.ty_le_quy_doi || 1),
                            han_su_dung: item.han_su_dung
                        }))
                    };
                },

                async submitForm() {
                    this.errorMessage = '';
                    this.successMessage = '';

                    const validationError = this.validateForm();

                    if (validationError) {
                        this.errorMessage = validationError;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        return;
                    }

                    if (!confirm('Xác nhận lập phiếu nhập và cộng tồn kho ngay?')) {
                        return;
                    }

                    const csrfMeta = document.querySelector('meta[name="csrf-token"]');

                    if (!csrfMeta) {
                        this.errorMessage = 'Thiếu CSRF token. Hãy thêm <meta name="csrf-token" content="{{ csrf_token() }}"> vào admin.layout.';
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        return;
                    }

                    const payload = this.buildPayload();

                    console.log('Payload gửi tạo phiếu nhập:', payload);

                    this.isSubmitting = true;

                    try {
                        const response = await fetch('{{ route('phieu-nhap.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfMeta.getAttribute('content')
                            },
                            body: JSON.stringify(payload)
                        });

                        const rawText = await response.text();

                        console.log('Status tạo phiếu:', response.status);
                        console.log('Raw response tạo phiếu:', rawText);

                        let result = null;

                        try {
                            result = JSON.parse(rawText);
                        } catch (e) {
                            this.errorMessage = 'Server không trả JSON. Kiểm tra Console hoặc laravel.log để xem lỗi thật.';
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                            return;
                        }

                        if (response.ok && result.success) {
                            this.successMessage = result.message || 'Lập phiếu nhập thành công!';
                            window.scrollTo({ top: 0, behavior: 'smooth' });

                            setTimeout(() => {
                                window.location.href = result.redirect || '{{ route('phieu-nhap.index') }}';
                            }, 700);

                            return;
                        }

                        if (response.status === 422 && result.errors) {
                            this.errorMessage = Object.values(result.errors).flat().join(' ');
                        } else {
                            this.errorMessage = result.message || 'Không thể lập phiếu nhập.';
                        }

                        window.scrollTo({ top: 0, behavior: 'smooth' });

                    } catch (error) {
                        console.error('Lỗi submit phiếu nhập:', error);
                        this.errorMessage = 'Không gửi được dữ liệu tạo phiếu nhập. Mở F12 > Console/Network để xem chi tiết.';
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                formatPrice(price) {
                    return new Intl.NumberFormat('vi-VN').format(price || 0) + '₫';
                },

                formatNumber(number) {
                    return new Intl.NumberFormat('vi-VN').format(number || 0);
                }
            }
        }
    </script>
@endsection