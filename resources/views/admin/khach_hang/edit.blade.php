@extends('admin.layout')

@section('title', 'Sửa Khách Hàng - Admin')
@section('page_title', 'Chỉnh sửa: ' . $khachHang->ten_khach_hang)

@section('content')
    <div class="mb-6">
        <a href="{{ route('khach-hang.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 font-bold text-sm rounded-xl hover:bg-slate-50 transition flex items-center gap-2 no-underline shadow-sm w-max">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại danh sách
        </a>
    </div>

    @if($errors->any())
        <div class="mb-6 px-6 py-4 bg-red-50 border border-red-200 text-red-600 rounded-2xl shadow-sm">
            <p class="font-black uppercase text-sm mb-2">Vui lòng kiểm tra lại:</p>
            <ul class="list-disc list-inside text-xs font-medium space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('khach-hang.update', $khachHang->id) }}" method="POST" class="max-w-3xl bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
        @csrf
        @method('PUT')
        
        <h3 class="text-base font-black text-slate-800 uppercase tracking-tighter mb-6 flex items-center gap-3 border-b border-slate-100 pb-4">
            <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            </div>
            Cập nhật thông tin
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Tên khách hàng <span class="text-red-500">*</span></label>
                <input type="text" name="ten_khach_hang" value="{{ old('ten_khach_hang', $khachHang->ten_khach_hang) }}" required
                    class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition font-bold text-slate-800">
            </div>

            <div>
                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Số điện thoại <span class="text-red-500">*</span></label>
                <input type="text" name="so_dien_thoai" value="{{ old('so_dien_thoai', $khachHang->so_dien_thoai) }}" required
                    class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition text-slate-800 font-bold">
            </div>

            <div>
                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Mã KH (Không thể sửa)</label>
                <input type="text" value="{{ $khachHang->ma_khach_hang }}" readonly
                    class="w-full px-5 py-3.5 bg-slate-100 border border-slate-200 rounded-2xl text-slate-500 font-bold cursor-not-allowed">
            </div>

            <div>
                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Điểm tích lũy (Sửa thủ công)</label>
                <input type="number" name="diem_tich_luy" value="{{ old('diem_tich_luy', $khachHang->diem_tich_luy) }}" min="0" required
                    class="w-full px-5 py-3.5 bg-blue-50 border border-blue-200 text-blue-700 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition font-black">
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Ghi chú thêm</label>
            <textarea name="ghi_chu" rows="3"
                class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition text-slate-800">{{ old('ghi_chu', $khachHang->ghi_chu) }}</textarea>
        </div>

        <div class="mb-8">
            <label class="relative inline-flex items-center cursor-pointer group">
                <input type="hidden" name="trang_thai" value="0">
                <input type="checkbox" name="trang_thai" value="1" class="sr-only peer" {{ old('trang_thai', $khachHang->trang_thai) ? 'checked' : '' }}>
                <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 shadow-inner"></div>
                <span class="ml-4 text-xs font-black text-slate-400 peer-checked:text-blue-600 uppercase tracking-widest transition-colors">Tài khoản Hoạt động</span>
            </label>
        </div>

        <button type="submit" class="w-full py-4 bg-emerald-600 text-white font-black rounded-2xl shadow-xl shadow-emerald-200 hover:bg-emerald-700 transition uppercase tracking-widest text-sm flex justify-center items-center gap-2">
            Cập nhật thay đổi
        </button>
    </form>
@endsection