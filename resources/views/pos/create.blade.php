<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Thuốc Mới - Duy Pharma</title>
    <!-- Sử dụng CDN tailwind để đồng bộ với trang Index -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 p-8 flex justify-center items-start min-h-screen">

    <div class="max-w-3xl w-full bg-white p-8 rounded-xl shadow-md">
        <!-- Breadcrumb mới bổ sung -->
        <nav class="flex mb-4 text-sm text-slate-500" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="/" class="hover:text-emerald-600">Trang chủ</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-slate-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <a href="{{ route('admin.thuoc.index') }}" class="hover:text-emerald-600">Quản lý thuốc</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-slate-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="text-slate-400 font-medium">Thêm mới</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Tiêu đề và nút quay lại -->
        <div class="flex items-center justify-between mb-8 border-b pb-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">➕ Thêm Thuốc Mới</h2>
                <p class="text-slate-500 text-sm">Nhập thông tin chi tiết để thêm thuốc vào hệ thống kho.</p>
            </div>
            <a href="{{ route('admin.thuoc.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </a>
        </div>

        <!-- Hiển thị lỗi Validation nếu có -->
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6 shadow-sm">
                <p class="font-bold mb-1">Đã có lỗi xảy ra:</p>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form nhập liệu -->
        <!-- Bổ sung enctype để có thể tải ảnh lên -->
        <form action="{{ route('admin.thuoc.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Mã thuốc -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Mã thuốc / Barcode <span class="text-red-500">*</span></label>
                    <input type="text" name="ma_thuoc" value="{{ old('ma_thuoc') }}" placeholder="VD: SP001" required
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                </div>

                <!-- Tên thuốc -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tên thương mại <span class="text-red-500">*</span></label>
                    <input type="text" name="ten_thuoc" value="{{ old('ten_thuoc') }}" placeholder="VD: Panadol Extra" required
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                </div>
            </div>

            <!-- Hoạt chất -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Hoạt chất chính</label>
                <input type="text" name="hoat_chat" value="{{ old('hoat_chat') }}" placeholder="VD: Paracetamol 500mg, Caffeine 65mg"
                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition bg-white">
            </div>

            <!-- Cấu trúc quy đổi -->
            <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                <h3 class="text-sm font-bold text-slate-700 mb-4 border-b border-slate-200 pb-2">Cấu trúc quy đổi (Nhập kho - Bán lẻ)</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Đơn vị nhập -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Đơn vị nhập <span class="text-red-500">*</span></label>
                        <input type="text" name="don_vi_nhap" value="{{ old('don_vi_nhap') }}" placeholder="VD: Hộp, Thùng..." required
                            class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition bg-white">
                    </div>

                    <!-- Tỷ lệ quy đổi -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Tỷ lệ quy đổi <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500 text-sm">1 ĐV Nhập =</span>
                            <input type="number" name="ty_le_quy_doi" value="{{ old('ty_le_quy_doi', 1) }}" min="1" required
                                class="w-full pl-28 pr-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition bg-white">
                        </div>
                    </div>

                    <!-- Đơn vị cơ bản -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Đơn vị bán lẻ <span class="text-red-500">*</span></label>
                        <input type="text" name="don_vi_co_ban" value="{{ old('don_vi_co_ban') }}" placeholder="VD: Viên, Vỉ..." required
                            class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition bg-white">
                    </div>
                </div>
            </div>

            <!-- Giá và Tồn kho -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Giá nhập -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Giá nhập (VNĐ) <span class="text-red-500">*</span></label>
                    <input type="number" name="gia_nhap" value="{{ old('gia_nhap', 0) }}" min="0" required
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition bg-white">
                </div>

                <!-- Giá bán -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Giá bán (VNĐ) <span class="text-red-500">*</span></label>
                    <input type="number" name="gia_ban" value="{{ old('gia_ban', 0) }}" min="0" required
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition bg-white">
                </div>

                <!-- Số lượng tồn -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tồn kho (ĐV lẻ) <span class="text-red-500">*</span></label>
                    <input type="number" name="so_luong_ton" value="{{ old('so_luong_ton', 0) }}" min="0" required
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition bg-white">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Đơn vị tính -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Đơn vị tính <span class="text-red-500">*</span></label>
                    <select name="don_vi_tinh" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition bg-white">
                        <option value="Viên" {{ old('don_vi_tinh') == 'Viên' ? 'selected' : '' }}>Viên</option>
                        <option value="Vỉ" {{ old('don_vi_tinh') == 'Vỉ' ? 'selected' : '' }}>Vỉ</option>
                        <option value="Hộp" {{ old('don_vi_tinh') == 'Hộp' ? 'selected' : '' }}>Hộp</option>
                        <option value="Chai" {{ old('don_vi_tinh') == 'Chai' ? 'selected' : '' }}>Chai</option>
                        <option value="Ống" {{ old('don_vi_tinh') == 'Ống' ? 'selected' : '' }}>Ống</option>
                    </select>
                </div>

                <!-- Hạn sử dụng -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Hạn sử dụng</label>
                    <input type="date" name="han_su_dung" value="{{ old('han_su_dung') }}"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition bg-white">
                </div>
            </div>

            <!-- Hình ảnh sản phẩm -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Hình ảnh thuốc (Tùy chọn)</label>
                <div class="flex items-center gap-6">
                    <div class="shrink-0">
                        <img id="image-preview" src="https://placehold.co/150x150?text=No+Image" alt="Preview" class="h-24 w-24 object-cover rounded-lg border border-slate-200 bg-slate-50 shadow-sm">
                    </div>
                    <div class="flex-1">
                        <input type="file" name="hinh_anh" id="hinh_anh" accept="image/*" onchange="previewImage(event)"
                            class="w-full px-3 py-1.5 rounded-lg border border-slate-300 file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition bg-white">
                        <p class="text-xs text-slate-500 mt-2">Hỗ trợ định dạng: JPG, PNG, WEBP. Dung lượng tối đa: 2MB.</p>
                    </div>
                </div>
            </div>

            <!-- Nhóm nút hành động -->
            <div class="flex items-center justify-end gap-4 pt-6 border-t mt-8">
                <a href="{{ route('admin.thuoc.index') }}" 
                   class="px-6 py-2.5 rounded-lg text-slate-600 font-semibold hover:bg-slate-100 transition">
                    Hủy bỏ
                </a>
                <button type="submit" 
                        class="px-8 py-2.5 bg-emerald-600 text-white font-bold rounded-lg hover:bg-emerald-700 shadow-lg shadow-emerald-200 transition">
                    Lưu thông tin
                </button>
            </div>
        </form>
    </div>

    <!-- Script xem trước hình ảnh ngay khi chọn file -->
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('image-preview');
                output.src = reader.result;
            };
            if(event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }
    </script>
</body>
</html>