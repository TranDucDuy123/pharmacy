@extends('admin.layout')

@section('title', 'Dashboard - Quản Lý Hệ Thống')
@section('page_title', 'Bảng Điều Khiển Tổng Quan')

@section('content')
    <!-- Tích hợp thư viện Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- THANH ĐIỀU HƯỚNG THỜI GIAN -->
    <div class="mb-8 flex flex-wrap gap-4 justify-between items-center">
        <div class="animate-fade-in">
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">DYLY Pharma Analytics</h2>
            <p class="text-sm text-slate-500 font-medium">Báo cáo hiệu suất kinh doanh từ hệ thống POS.</p>
        </div>
        <div class="bg-white rounded-2xl p-1.5 border border-slate-200 inline-flex shadow-sm">
            @foreach(['today' => 'Hôm nay', 'week' => 'Tuần này', 'month' => 'Tháng này'] as $key => $label)
                <a href="{{ route('admin.dashboard', ['time_range' => $key]) }}" 
                   class="px-5 py-2 text-xs font-bold rounded-xl transition-all duration-300 {{ (isset($timeRange) && $timeRange == $key) ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-slate-500 hover:bg-slate-50' }} no-underline">
                   {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- CÁC CHỈ SỐ QUAN TRỌNG (CARDS) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Doanh thu -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 transition-all hover:shadow-xl hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Doanh thu</span>
            </div>
            <h4 class="text-2xl font-black text-slate-800 tracking-tighter">{{ number_format($doanhThu ?? 0, 0, ',', '.') }}₫</h4>
            <div class="mt-2 text-xs font-bold flex items-center gap-1 {{ ($phanTramTangTruong ?? 0) >= 0 ? 'text-emerald-500' : 'text-pink-500' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ ($phanTramTangTruong ?? 0) >= 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6' }}"></path></svg>
                {{ number_format(abs($phanTramTangTruong ?? 0), 1) }}% <span class="text-slate-400 font-medium">so với {{ $textPrevTime ?? 'kỳ trước' }}</span>
            </div>
        </div>

        <!-- Đơn hàng -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 transition-all hover:shadow-xl hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <span class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Đơn hàng</span>
            </div>
            <h4 class="text-2xl font-black text-slate-800 tracking-tighter">{{ $soDonHang ?? 0 }} <small class="text-sm font-bold text-slate-400">Giao dịch</small></h4>
            <p class="mt-2 text-[11px] font-bold text-slate-400 italic">Trong {{ $textTime ?? 'hôm nay' }}</p>
        </div>

        <!-- Hết hạn -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 transition-all hover:shadow-xl hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-pink-50 text-pink-600 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-[10px] font-black uppercase text-pink-400 tracking-widest">Sắp hết hạn</span>
            </div>
            <h4 class="text-2xl font-black text-pink-600 tracking-tighter">{{ $thuocSapHetHan ?? 0 }} <small class="text-sm font-bold text-pink-300">Lô thuốc</small></h4>
            <p class="mt-2 text-[11px] font-bold text-slate-400 italic">Cảnh báo: Hạn < 90 ngày</p>
        </div>

        <!-- Cạn kho -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 transition-all hover:shadow-xl hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <span class="text-[10px] font-black uppercase text-amber-400 tracking-widest">Sắp cạn kho</span>
            </div>
            <h4 class="text-2xl font-black text-amber-700 tracking-tighter">{{ $thuocSapCanKho ?? 0 }} <small class="text-sm font-bold text-amber-400">Sản phẩm</small></h4>
            <p class="mt-2 text-[11px] font-bold text-slate-400 italic">Tồn kho hiện tại <= 10</p>
        </div>
    </div>

    <!-- BIỂU ĐỒ XU HƯỚNG & TẦN SUẤT (ROW 1) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Biểu đồ Doanh thu 7 ngày -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
            <h3 class="font-black text-slate-800 text-sm uppercase tracking-widest flex items-center gap-2 mb-6">
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                Biến động doanh thu (7 Ngày qua)
            </h3>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Biểu đồ Tần suất theo giờ -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
            <h3 class="font-black text-slate-800 text-sm uppercase tracking-widest flex items-center gap-2 mb-6">
                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                Phân bổ doanh thu theo khung giờ
            </h3>
            <div class="h-64">
                <canvas id="hourlyChart"></canvas>
            </div>
        </div>
    </div>

    <!-- TOP SẢN PHẨM & CƠ CẤU (ROW 2) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Top 5 Sản phẩm bán chạy -->
        <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
            <h3 class="font-black text-slate-800 text-sm uppercase tracking-widest flex items-center gap-2 mb-6">
                <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                Top 5 thuốc bán chạy nhất ({{ $textTime ?? 'hiện tại' }})
            </h3>
            <div class="h-64">
                <canvas id="topProductChart"></canvas>
            </div>
        </div>

        <!-- Cơ cấu Kho hàng -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 flex flex-col items-center">
            <h3 class="font-black text-slate-800 text-sm uppercase tracking-widest mb-6">Cơ cấu danh mục thuốc</h3>
            <div class="w-full h-56">
                <canvas id="categoryChart"></canvas>
            </div>
           <div class="space-y-4">
            @isset($categoryData)
                @foreach($categoryData as $idx => $cat)
                    <div class="flex items-center justify-between group">
                        <div class="flex items-center gap-3">
                            <!-- Màu sắc icon thay đổi theo index -->
                            <div class="w-2 h-2 rounded-full" style="background-color: {{ ['#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#14b8a6', '#f43f5e'][$idx % 7] }}"></div>
                            <span class="text-xs font-bold text-slate-600 group-hover:text-blue-600 transition-colors">{{ $cat->danh_muc }}</span>
                        </div>
                        <span class="bg-slate-50 text-[10px] font-black text-slate-400 px-2 py-0.5 rounded-full border border-slate-100">{{ $cat->total }} thuốc</span>
                    </div>
                @endforeach
            @else
                <p class="text-xs text-slate-400 italic">Chưa có dữ liệu danh mục.</p>
            @endisset
        </div>
        </div>
    </div>

    <!-- DANH SÁCH CẢNH BÁO KHO -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Cảnh báo cạn kho -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-amber-50 bg-amber-50/20 flex items-center justify-between">
                <h3 class="font-bold text-amber-900 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Sản phẩm sắp cạn kho
                </h3>
                <a href="{{ route('thuoc.index', ['stock' => 'sap_het']) }}" class="text-[10px] font-black uppercase text-amber-600 hover:underline no-underline">Xem tất cả</a>
            </div>
            <table class="w-full text-xs">
                <tbody class="divide-y divide-slate-100">
                    @isset($danhSachSapCanKho)
                        @forelse($danhSachSapCanKho as $thuoc)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 font-bold text-slate-700">{{ $thuoc->ten_thuoc }}</td>
                                <td class="px-6 py-4 text-right">
                                    <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded-lg font-black uppercase text-[10px]">Còn {{ $thuoc->so_luong_ton }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="px-6 py-10 text-center text-slate-400 italic">Không có thuốc sắp hết.</td></tr>
                        @endforelse
                    @endisset
                </tbody>
            </table>
        </div>

        <!-- Cảnh báo hết hạn -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-pink-50 bg-pink-50/20 flex items-center justify-between">
                <h3 class="font-bold text-pink-900 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Lô hàng sắp hết hạn sử dụng
                </h3>
            </div>
            <table class="w-full text-xs">
                <tbody class="divide-y divide-slate-100">
                    @isset($danhSachSapHetHan)
                        @forelse($danhSachSapHetHan as $thuoc)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 font-bold text-slate-700">{{ $thuoc->ten_thuoc }}</td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-black text-pink-600">{{ $thuoc->han_su_dung->format('d/m/Y') }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="px-6 py-10 text-center text-slate-400 italic">Không có lô thuốc sắp hết hạn.</td></tr>
                        @endforelse
                    @endisset
                </tbody>
            </table>
        </div>
    </div>

    <!-- SCRIPTS KHỞI TẠO BIỂU ĐỒ -->
    <script>

        // Kiểm tra xem dữ liệu từ Controller truyền sang có rỗng không
            let testLabels = {!! isset($chartLabels) ? json_encode($chartLabels) : '"Không có biến $chartLabels"' !!};
            let testData = {!! isset($chartData) ? json_encode($chartData) : '"Không có biến $chartData"' !!};
            
            console.log("Dữ liệu Nhãn (X):", testLabels);
            console.log("Dữ liệu Số (Y):", testData);
            
        const chartBaseOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: '#f1f5f9', drawBorder: false }, ticks: { font: { size: 10, weight: 'bold' } } },
                x: { grid: { display: false }, ticks: { font: { size: 10, weight: 'bold' } } }
            }
        };

        // 1. Biểu đồ Đường: Doanh thu 7 ngày
        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: {!! isset($chartLabels) ? json_encode($chartLabels) : '[]' !!},
                datasets: [{
                    data: {!! isset($chartData) ? json_encode($chartData) : '[]' !!},
                    borderColor: '#3b82f6', backgroundColor: 'rgba(59, 130, 246, 0.05)',
                    borderWidth: 4, fill: true, tension: 0.4, pointRadius: 0, pointHoverRadius: 6
                }]
            },
            options: chartBaseOptions
        });

        // 2. Biểu đồ Cột: Theo giờ
        new Chart(document.getElementById('hourlyChart'), {
            type: 'bar',
            data: {
                labels: {!! isset($hourlyLabels) ? json_encode($hourlyLabels) : '[]' !!},
                datasets: [{
                    data: {!! isset($hourlyData) ? json_encode($hourlyData) : '[]' !!},
                    backgroundColor: '#8b5cf6', borderRadius: 6
                }]
            },
            options: chartBaseOptions
        });

        // 3. Biểu đồ Thanh ngang: Top Sản phẩm
        new Chart(document.getElementById('topProductChart'), {
            type: 'bar',
            data: {
                labels: {!! isset($topProductLabels) ? json_encode($topProductLabels) : '[]' !!},
                datasets: [{
                    data: {!! isset($topProductData) ? json_encode($topProductData) : '[]' !!},
                    backgroundColor: '#10b981', borderRadius: 6
                }]
            },
            options: { ...chartBaseOptions, indexAxis: 'y' }
        });

        // 4. Biểu đồ Tròn: Danh mục
        new Chart(document.getElementById('categoryChart'), {
            type: 'doughnut',
            data: {
                labels: {!! isset($categoryData) ? json_encode($categoryData->pluck('danh_muc')) : '[]' !!},
                datasets: [{
                    data: {!! isset($categoryData) ? json_encode($categoryData->pluck('total')) : '[]' !!},
                    backgroundColor: ['#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981'],
                    borderWidth: 0, cutout: '75%'
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    </script>
@endsection