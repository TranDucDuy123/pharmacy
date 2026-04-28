@extends('storefront.layout')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white p-8 rounded-3xl shadow-xl border border-slate-100">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 font-black text-2xl">D</div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Xin chào Quý khách!</h2>
            <p class="text-sm text-slate-500 mt-2 font-medium">Đăng nhập bằng số điện thoại để tiếp tục</p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 text-emerald-600 rounded-xl text-sm font-bold text-center border border-emerald-100 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 text-red-600 rounded-xl text-sm font-bold text-center border border-red-100 shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        <form class="space-y-6" action="{{ route('customer.login.post') }}" method="POST">
            @csrf
            <div>
                <label for="so_dien_thoai" class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Số điện thoại</label>
                <div class="relative">
                    <input id="so_dien_thoai" name="so_dien_thoai" type="tel" required value="{{ old('so_dien_thoai') }}"
                        class="appearance-none block w-full px-5 py-3.5 pl-12 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-bold focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition" placeholder="VD: 0901234567">
                    <svg class="w-5 h-5 text-slate-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                </div>
            </div>

            <div>
                <label for="password" class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Mật khẩu</label>
                <div class="relative">
                    <input id="password" name="password" type="password" required 
                        class="appearance-none block w-full px-5 py-3.5 pl-12 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-bold focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition" placeholder="••••••••">
                    <svg class="w-5 h-5 text-slate-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm font-medium text-slate-600">Ghi nhớ đăng nhập</label>
                </div>
                <a href="#" class="text-sm font-bold text-blue-600 hover:text-blue-500 text-decoration-none">Quên mật khẩu?</a>
            </div>

            <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-2xl shadow-lg shadow-blue-200 text-sm font-black text-white bg-blue-600 hover:bg-blue-700 hover:-translate-y-0.5 transition-all uppercase tracking-widest">
                Đăng nhập ngay
            </button>
        </form>

        <div class="mt-8 text-center text-sm text-slate-500 font-medium">
            Chưa có tài khoản? 
            <a href="{{ route('customer.register') }}" class="font-bold text-blue-600 hover:text-blue-500 text-decoration-none">Đăng ký thành viên mới</a>
        </div>
    </div>
</div>
@endsection