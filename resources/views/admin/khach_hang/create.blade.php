@extends('admin.layout')

@section('title', 'Thêm Khách Hàng - Admin')
@section('page_title', 'Thêm Khách Hàng Mới')

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

    <form action="{{ route('khach-hang.store') }}" method="POST" class="max-w-3xl bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
        @csrf
        
        <h3 class="text-base font-black text-slate-800 uppercase tracking-tighter mb-6 flex items-center gap-3 border-b border-slate-100 pb-4">
            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </div>
            Thông tin liên hệ
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Tên khách hàng <span class="text-red-500">*</span></label>
                <input type="text" name="ten_khach_hang" value="{{ old('ten_khach_hang') }}" required placeholder="VD: Nguyễn Văn A"
                    class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition font-bold text-slate-800">
            </div>

            <div>
                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Số điện thoại <span class="text-red-500">*</span></label>
                <input type="text" name="so_dien_thoai" value="{{ old('so_dien_thoai') }}" required placeholder="VD: 0901234567"
                    class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition text-slate-800 font-bold">
            </div>

            <div>
                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Mã KH (Tự tạo nếu trống)</label>
                <input type="text" name="ma_khach_hang" value="{{ old('ma_khach_hang') }}" placeholder="VD: KH0005"
                    class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition text-slate-800">
            </div>

            <div>
                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Điểm tích lũy ban đầu</label>
                <input type="number" name="diem_tich_luy" value="{{ old('diem_tich_luy', 0) }}" min="0"
                    class="w-full px-5 py-3.5 bg-blue-50 border border-blue-200 text-blue-700 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition font-black">
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Ghi chú thêm</label>
            <textarea name="ghi_chu" rows="3" placeholder="Sở thích, bệnh lý nền..."
                class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none transition text-slate-800">{{ old('ghi_chu') }}</textarea>
        </div>

        <div class="mb-8">
            <label class="relative inline-flex items-center cursor-pointer group">
                <input type="checkbox" name="trang_thai" value="1" class="sr-only peer" checked>
                <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 shadow-inner"></div>
                <span class="ml-4 text-xs font-black text-slate-400 peer-checked:text-blue-600 uppercase tracking-widest transition-colors">Tài khoản Hoạt động</span>
            </label>
        </div>

        <button type="submit" class="w-full py-4 bg-blue-600 text-white font-black rounded-2xl shadow-xl shadow-blue-200 hover:bg-blue-700 transition uppercase tracking-widest text-sm flex justify-center items-center gap-2">
            Lưu khách hàng
        </button>
    </form>
@endsection