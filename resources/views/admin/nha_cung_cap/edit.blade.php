@extends('admin.layout')

@section('title', 'Cập nhật Nhà Cung Cấp')
@section('page_title', 'Sửa Nhà Cung Cấp: ' . $nhaCungCap->ten_ncc)

@section('content')
    <div class="mb-6">
        <a href="{{ route('nha-cung-cap.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 font-bold text-sm rounded-xl hover:bg-slate-50 transition flex items-center gap-2 no-underline shadow-sm w-max">
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

    <form action="{{ route('nha-cung-cap.update', $nhaCungCap->id) }}" method="POST" class="max-w-4xl bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
        @csrf
        @method('PUT')
        
        <h3 class="text-base font-black text-slate-800 uppercase tracking-tighter mb-6 flex items-center gap-3 border-b border-slate-100 pb-4">
            <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </div>
            Thông tin cập nhật
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="md:col-span-2">
                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Tên nhà cung cấp <span class="text-red-500">*</span></label>
                <input type="text" name="ten_ncc" value="{{ old('ten_ncc', $nhaCungCap->ten_ncc) }}" required
                    class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-100 outline-none transition font-bold text-slate-800">
            </div>

            <div>
                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Mã NCC <span class="text-red-500">*</span></label>
                <input type="text" name="ma_ncc" value="{{ old('ma_ncc', $nhaCungCap->ma_ncc) }}" required
                    class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-100 outline-none transition text-slate-800 font-bold uppercase">
            </div>

            <div>
                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Mã số thuế</label>
                <input type="text" name="ma_so_thue" value="{{ old('ma_so_thue', $nhaCungCap->ma_so_thue) }}"
                    class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-100 outline-none transition text-slate-800">
            </div>

            <div>
                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Số điện thoại</label>
                <input type="text" name="so_dien_thoai" value="{{ old('so_dien_thoai', $nhaCungCap->so_dien_thoai) }}"
                    class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-100 outline-none transition text-slate-800">
            </div>

            <div>
                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Email liên hệ</label>
                <input type="email" name="email" value="{{ old('email', $nhaCungCap->email) }}"
                    class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-100 outline-none transition text-slate-800">
            </div>

            <div class="md:col-span-2">
                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Địa chỉ</label>
                <input type="text" name="dia_chi" value="{{ old('dia_chi', $nhaCungCap->dia_chi) }}"
                    class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-100 outline-none transition text-slate-800">
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Ghi chú</label>
            <textarea name="ghi_chu" rows="3"
                class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-100 outline-none transition text-slate-800">{{ old('ghi_chu', $nhaCungCap->ghi_chu) }}</textarea>
        </div>

        <div class="mb-8">
            <label class="relative inline-flex items-center cursor-pointer group">
                <input type="hidden" name="trang_thai" value="0">
                <input type="checkbox" name="trang_thai" value="1" class="sr-only peer" {{ old('trang_thai', $nhaCungCap->trang_thai) ? 'checked' : '' }}>
                <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600 shadow-inner"></div>
                <span class="ml-4 text-xs font-black text-slate-400 peer-checked:text-emerald-600 uppercase tracking-widest transition-colors">Đang Hợp Tác</span>
            </label>
        </div>

        <button type="submit" class="w-full py-4 bg-emerald-600 text-white font-black rounded-2xl shadow-xl shadow-emerald-200 hover:bg-emerald-700 transition uppercase tracking-widest text-sm flex justify-center items-center gap-2">
            Lưu Thay Đổi
        </button>
    </form>
@endsection