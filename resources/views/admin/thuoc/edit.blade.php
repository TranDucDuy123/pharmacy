@extends('admin.layout')

@section('title', 'Cập nhật Thuốc - Quản lý Kho')
@section('page_title', 'Chỉnh Sửa: ' . $thuoc->ten_thuoc)

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('thuoc.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 font-bold text-sm rounded-xl hover:bg-slate-50 transition flex items-center gap-2 no-underline shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại kho thuốc
        </a>
    </div>

    @if($errors->any())
        <div class="mb-6 px-6 py-4 bg-red-50 border border-red-200 text-red-600 rounded-2xl shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                <span class="font-black uppercase text-sm tracking-widest">Dữ liệu không hợp lệ</span>
            </div>
            <ul class="list-disc list-inside text-xs font-medium space-y-1 ml-9">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('thuoc.update', $thuoc->id) }}" method="POST" enctype="multipart/form-data" 
          class="max-w-6xl space-y-8" 
          x-data="{ 
              donViCoBan: '{{ old('don_vi_co_ban', $thuoc->don_vi_co_ban) }}', 
              donViNhap: '{{ old('don_vi_nhap', $thuoc->don_vi_nhap) }}',
              tyLe: {{ (int) old('ty_le_quy_doi', $thuoc->ty_le_quy_doi ?? 1) }},
              giaNhap: {{ (int) old('gia_nhap', $thuoc->gia_nhap ?? 0) }},
              giaBan: {{ (int) old('gia_ban', $thuoc->gia_ban ?? 0) }}
          }">
        @csrf
        @method('PUT')

        <!-- ĐỒNG BỘ TƯƠNG THÍCH NGƯỢC (Hidden don_vi_tinh) -->
        <input type="hidden" name="don_vi_tinh" :value="donViCoBan">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <!-- THÔNG TIN CƠ BẢN -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
                    <h3 class="text-base font-black text-slate-800 uppercase tracking-tighter mb-6 flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        Thông tin sản phẩm
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Tên thuốc thương mại <span class="text-red-500">*</span></label>
                            <input type="text" name="ten_thuoc" value="{{ old('ten_thuoc', $thuoc->ten_thuoc) }}" required
                                class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition font-bold text-slate-800">
                        </div>

                        <div>
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Mã thuốc / Barcode <span class="text-red-500">*</span></label>
                            <input type="text" name="ma_thuoc" value="{{ old('ma_thuoc', $thuoc->ma_thuoc) }}" required
                                class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition text-slate-800 font-black uppercase">
                        </div>

                        <div>
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Hoạt chất chính</label>
                            <input type="text" name="hoat_chat" value="{{ old('hoat_chat', $thuoc->hoat_chat) }}" 
                                class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition text-slate-800">
                        </div>

                        <div>
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Nhóm danh mục <span class="text-red-500">*</span></label>
                            <select name="danh_muc" required class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition font-bold text-slate-700">
                                @foreach(['Giảm đau - Hạ sốt', 'Kháng sinh', 'Hệ hô hấp', 'Tiêu hóa', 'Tim mạch', 'Thần kinh', 'Vitamin & Thực phẩm chức năng', 'Vật tư y tế'] as $dm)
                                    <option value="{{ $dm }}" {{ old('danh_muc', $thuoc->danh_muc) == $dm ? 'selected' : '' }}>{{ $dm }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Quy chế kinh doanh <span class="text-red-500">*</span></label>
                            <select name="loai_thuoc" required class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition font-bold text-slate-700">
                                <option value="OTC" {{ old('loai_thuoc', $thuoc->loai_thuoc) == 'OTC' ? 'selected' : '' }}>OTC (Không kê đơn)</option>
                                <option value="Rx" {{ old('loai_thuoc', $thuoc->loai_thuoc) == 'Rx' ? 'selected' : '' }}>Rx (Kê đơn)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- QUY ĐỔI ĐƠN VỊ -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-full -mr-16 -mt-16 opacity-50"></div>
                    <h3 class="text-base font-black text-slate-800 uppercase tracking-tighter mb-8 flex items-center gap-3 relative">
                        <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        </div>
                        Đơn vị & Quy đổi
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative">
                        <div>
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Đơn vị cơ bản (Lẻ) <span class="text-red-500">*</span></label>
                            <input type="text" name="don_vi_co_ban" x-model="donViCoBan" value="{{ old('don_vi_co_ban', $thuoc->don_vi_co_ban) }}" required
                                class="w-full px-5 py-3.5 bg-blue-50/50 border border-blue-200 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition font-black text-blue-700">
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Đơn vị nhập (Sỉ)</label>
                            <input type="text" name="don_vi_nhap" x-model="donViNhap" value="{{ old('don_vi_nhap', $thuoc->don_vi_nhap) }}"
                                class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition text-slate-800 font-bold">
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Tỷ lệ (1 <span x-text="donViNhap || 'Sỉ'"></span> = ? lẻ)</label>
                            <input type="number" name="ty_le_quy_doi" x-model="tyLe" value="{{ old('ty_le_quy_doi', $thuoc->ty_le_quy_doi) }}" min="1" required
                                class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition text-slate-800 font-black">
                        </div>
                    </div>
                </div>

                <!-- ẢNH SẢN PHẨM & VỊ TRÍ -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Vị trí kệ</label>
                            <input type="text" name="vi_tri" value="{{ old('vi_tri', $thuoc->vi_tri) }}" 
                                class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition text-slate-800">
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Cập nhật ảnh</label>
                            <input type="file" name="hinh_anh" accept="image/*"
                                class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-blue-600 file:text-white hover:file:bg-blue-700 transition">
                            @if($thuoc->hinh_anh)
                                <div class="mt-3 flex items-center gap-3 p-3 bg-slate-50 border border-slate-100 rounded-xl">
                                    <img src="{{ asset($thuoc->hinh_anh) }}" alt="{{ $thuoc->ten_thuoc }}" class="w-12 h-12 rounded-lg object-cover border border-slate-200 shadow-sm">
                                    
                                    <div class="flex flex-col overflow-hidden">
                                        <span class="text-[10px] text-slate-400 uppercase tracking-widest mb-0.5">Ảnh hiện tại</span>
                                        
                                        <span class="text-xs font-bold text-blue-600 truncate" title="{{ basename($thuoc->hinh_anh) }}">
                                            {{ basename($thuoc->hinh_anh) }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <!-- TÀI CHÍNH & TỒN KHO -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
                    <h3 class="text-base font-black text-slate-800 uppercase tracking-tighter mb-8 flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        Giá & Kho
                    </h3>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Giá nhập (1 <span x-text="donViCoBan || 'Lẻ'"></span>)</label>
                            <div class="relative">
                                <input type="number" name="gia_nhap" x-model="giaNhap" value="{{ old('gia_nhap', $thuoc->gia_nhap) }}" required min="0" step="100"
                                    class="w-full pl-5 pr-12 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-100 outline-none transition font-bold text-slate-800">
                                <span class="absolute right-5 top-4 text-slate-400 font-bold text-sm">₫</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Giá bán (1 <span x-text="donViCoBan || 'Lẻ'"></span>)</label>
                            <div class="relative">
                                <input type="number" name="gia_ban" x-model="giaBan" value="{{ old('gia_ban', $thuoc->gia_ban) }}" required min="0" step="100"
                                    class="w-full pl-5 pr-12 py-4 bg-blue-50 border border-blue-200 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition text-blue-700 font-black text-2xl">
                                <span class="absolute right-5 top-5 text-blue-400 font-bold text-lg">₫</span>
                            </div>
                        </div>

                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex justify-between items-center" x-show="giaBan > 0 && giaNhap > 0">
                            <span class="text-[10px] font-black text-slate-400 uppercase">Lãi dự kiến</span>
                            <span class="text-sm font-black text-emerald-600" x-text="new Intl.NumberFormat('vi-VN').format(giaBan - giaNhap) + '₫ / ' + donViCoBan"></span>
                        </div>

                        <div class="pt-6 border-t border-slate-100">
                            <div class="flex justify-between items-end mb-2">
                                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest">Tồn kho (<span x-text="donViCoBan || 'Lẻ'"></span>)</label>
                                <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">{{ $thuoc->ton_kho_hien_thi }}</span>
                            </div>
                            <input type="number" name="so_luong_ton" value="{{ old('so_luong_ton', $thuoc->so_luong_ton ?? 0) }}" min="0" required
                                class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition text-slate-800 font-black text-lg">
                        </div>

                        <div>
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Hạn sử dụng lô này</label>
                            <input type="date" name="han_su_dung" value="{{ old('han_su_dung', $thuoc->han_su_dung ? $thuoc->han_su_dung->format('Y-m-d') : '') }}" required
                                class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition text-slate-600 font-bold">
                        </div>

                        <div class="pt-4">
                            <label class="relative inline-flex items-center cursor-pointer group">
                                <input type="hidden" name="trang_thai" value="0">
                                <input type="checkbox" name="trang_thai" value="1" class="sr-only peer" {{ old('trang_thai', $thuoc->trang_thai) ? 'checked' : '' }}>
                                <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 shadow-inner"></div>
                                <span class="ml-4 text-xs font-black text-slate-400 peer-checked:text-blue-600 uppercase tracking-widest transition-colors">Đang bán</span>
                            </label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full py-5 bg-emerald-600 text-white font-black rounded-3xl shadow-xl shadow-emerald-200 hover:bg-emerald-700 hover:-translate-y-1 active:scale-95 transition-all uppercase tracking-widest text-sm flex justify-center items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Lưu Thay Đổi
                </button>
            </div>
        </div>
    </form>
@endsection