<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Thuoc;
use App\Models\Order; 
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Thêm dòng này để dùng được hàm DB::raw()
use App\Models\OrderItem; // Thêm dòng này để gọi Model OrderItem


class AdminController extends Controller
{
    /**
     * 1. Hiển thị trang chủ Tổng quan của Admin (Dashboard)
     * Đã cập nhật logic lọc thời gian và lấy chi tiết cảnh báo kho
     */
    public function index(Request $request)
    {
         $timeRange = $request->query('time_range', 'today');
        $now = Carbon::now();

        // --- PHẦN 1: LOGIC LỌC THỜI GIAN ---
        if ($timeRange === 'week') {
            $startDate = $now->copy()->startOfWeek();
            $prevStartDate = $now->copy()->subWeek()->startOfWeek();
            $prevEndDate = $now->copy()->subWeek()->endOfWeek();
            $textTime = 'Tuần này'; $textPrevTime = 'tuần trước';
        } elseif ($timeRange === 'month') {
            $startDate = $now->copy()->startOfMonth();
            $prevStartDate = $now->copy()->subMonth()->startOfMonth();
            $prevEndDate = $now->copy()->subMonth()->endOfMonth();
            $textTime = 'Tháng này'; $textPrevTime = 'tháng trước';
        } else {
            $startDate = $now->copy()->startOfDay();
            $prevStartDate = $now->copy()->subDay()->startOfDay();
            $prevEndDate = $now->copy()->subDay()->endOfDay();
            $textTime = 'Hôm nay'; $textPrevTime = 'hôm qua';
        }

        // Thống kê thẻ Card
        $doanhThu = Order::where('created_at', '>=', $startDate)->where('status', 'completed')->sum('total_price');
        $soDonHang = Order::where('created_at', '>=', $startDate)->where('status', 'completed')->count();
        $doanhThuTruoc = Order::whereBetween('created_at', [$prevStartDate, $prevEndDate])->where('status', 'completed')->sum('total_price');
        $phanTramTangTruong = $doanhThuTruoc > 0 ? (($doanhThu - $doanhThuTruoc) / $doanhThuTruoc) * 100 : ($doanhThu > 0 ? 100 : 0);

        // --- PHẦN 2: DỮ LIỆU BIỂU ĐỒ DOANH THU 7 NGÀY GẦN NHẤT ---
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels[] = $date->format('d/m');
            $chartData[] = Order::whereDate('created_at', $date)->where('status', 'completed')->sum('total_price');
        }

        // --- PHẦN 3: BIỂU ĐỒ TOP 5 SẢN PHẨM BÁN CHẠY NHẤT ---
        $topProducts = OrderItem::select('thuoc_id', DB::raw('SUM(quantity) as total_qty'))
            ->whereHas('order', function($q) use ($startDate) {
                $q->where('created_at', '>=', $startDate)->where('status', 'completed');
            })
            ->groupBy('thuoc_id')
            ->orderBy('total_qty', 'desc')
            ->with('thuoc:id,ten_thuoc')
            ->take(5)
            ->get();
        
        $topProductLabels = $topProducts->map(fn($p) => $p->thuoc->ten_thuoc ?? 'N/A')->toArray();
        $topProductData = $topProducts->pluck('total_qty')->toArray();

        // --- PHẦN 4: DOANH THU THEO KHUNG GIỜ ---
        $hourlySales = Order::select(DB::raw('HOUR(created_at) as hour'), DB::raw('SUM(total_price) as total'))
            ->where('created_at', '>=', $startDate)
            ->where('status', 'completed')
            ->groupBy('hour')
            ->orderBy('hour', 'asc')
            ->get()
            ->pluck('total', 'hour')
            ->toArray();
        
        $hourlyLabels = [];
        $hourlyData = [];
        for ($h = 0; $h < 24; $h++) {
            $hourlyLabels[] = $h . 'h';
            $hourlyData[] = $hourlySales[$h] ?? 0;
        }

        // --- PHẦN 5: BIỂU ĐỒ CƠ CẤU DANH MỤC (QUAN TRỌNG) ---
        $categoryData = Thuoc::select('danh_muc', DB::raw('count(*) as total'))
                            ->whereNotNull('danh_muc')
                            ->groupBy('danh_muc')
                            ->orderBy('total', 'desc')
                            ->get();

        // Cảnh báo kho
        $querySapHetHan = Thuoc::dangKinhDoanh()->whereNotNull('han_su_dung')->where('han_su_dung', '<=', Carbon::now()->addDays(90));
        $thuocSapHetHan = $querySapHetHan->count();
        $danhSachSapHetHan = $querySapHetHan->orderBy('han_su_dung', 'asc')->take(5)->get();
                               
        $querySapCanKho = Thuoc::dangKinhDoanh()->where('so_luong_ton', '<=', 10);
        $thuocSapCanKho = $querySapCanKho->count();
        $danhSachSapCanKho = $querySapCanKho->orderBy('so_luong_ton', 'asc')->take(5)->get();

        $donHangMoi = Order::with(['items.thuoc', 'nhanVien'])->orderBy('created_at', 'desc')->take(5)->get();

        // Trả về view với đầy đủ biến
        return view('admin.dashboard', compact(
            'timeRange', 'textTime', 'textPrevTime',
            'doanhThu', 'doanhThuTruoc', 'phanTramTangTruong',
            'soDonHang', 'thuocSapHetHan', 'thuocSapCanKho', 
            'donHangMoi', 'danhSachSapHetHan', 'danhSachSapCanKho',
            'chartLabels', 'chartData', 'categoryData',
            'topProductLabels', 'topProductData', 'hourlyLabels', 'hourlyData'
        ));
    }

    /**
     * 2. Hiển thị danh sách hóa đơn (Orders Index)
     */
    public function ordersIndex(Request $request)
    {
        $query = Order::with('nhanVien')->latest();

        if ($request->filled('search')) {
            $search = str_replace(['#ORD-', 'ORD-'], '', $request->search);
            $query->where('id', (int)$search);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $danhSachHoaDon = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('danhSachHoaDon'));
    }

    /**
     * 3. Hiển thị chi tiết một hóa đơn (Order Show)
     */
    public function orderShow($id)
    {
        $order = Order::with(['nhanVien', 'items.thuoc'])->findOrFail($id);
        
        return view('admin.orders.show', compact('order'));
    }
}