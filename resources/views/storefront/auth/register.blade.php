@extends('storefront.layout')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white p-8 rounded-3xl shadow-xl border border-slate-100">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Trở thành thành viên</h2>
            <p class="text-sm text-slate-500 mt-2 font-medium">Đăng ký để tích điểm và nhận ưu đãi riêng</p>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 text-red-600 rounded-xl text-xs font-bold border border-red-100 shadow-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form class="space-y-5" action="{{ route('customer.register.post') }}" method="POST">
            @csrf
            
            <div>
                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Họ và tên <span class="text-red-500">*</span></label>
                <input name="ten_khach_hang" type="text" required value="{{ old('ten_khach_hang') }}"
                    class="appearance-none block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="VD: Nguyễn Văn A">
            </div>

            <div>
                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Số điện thoại (Dùng đăng nhập) <span class="text-red-500">*</span></label>
                <input name="so_dien_thoai" type="tel" required value="{{ old('so_dien_thoai') }}"
                    class="appearance-none block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="VD: 0901234567">
            </div>

            <div>
                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Mật khẩu <span class="text-red-500">*</span></label>
                <input name="password" type="password" required 
                    class="appearance-none block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Ít nhất 6 ký tự">
            </div>

            <div>
                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Xác nhận mật khẩu <span class="text-red-500">*</span></label>
                <input name="password_confirmation" type="password" required 
                    class="appearance-none block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Nhập lại mật khẩu">
            </div>

            <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-2xl shadow-lg shadow-indigo-200 text-sm font-black text-white bg-indigo-600 hover:bg-indigo-700 hover:-translate-y-0.5 transition-all uppercase tracking-widest mt-6">
                Đăng ký tài khoản
            </button>
        </form>

        <div class="mt-8 text-center text-sm text-slate-500 font-medium">
            Đã có tài khoản? 
            <a href="{{ route('customer.login') }}" class="font-bold text-indigo-600 hover:text-indigo-500 text-decoration-none">Đăng nhập tại đây</a>
        </div>
    </div>
</div>
@endsection