<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Thuoc;
use App\Models\Order; // THÊM DÒNG NÀY ĐỂ GỌI MODEL ORDER
use Illuminate\Support\Facades\Log;

class PosController extends Controller
{
    /**
     * Hiển thị giao diện bán hàng (POS)
     * Xử lý tìm kiếm, lọc theo danh mục, quy chế, giá và tồn kho
     */
    public function index(Request $request)
    {
        try {
            // 1. Lấy danh sách danh mục DUY NHẤT để làm menu lọc động
            $dsDanhMuc = Thuoc::select('danh_muc')
                ->whereNotNull('danh_muc')
                ->where('danh_muc', '<>', '')
                ->distinct()
                ->pluck('danh_muc');

            // 2. Khởi tạo Query Builder
            $query = Thuoc::query();

            // Mặc định chỉ hiện các thuốc đang kinh doanh (trang_thai = 1)
            $query->where('trang_thai', 1);

            // 3. Xử lý TÌM KIẾM (Search)
            if ($request->filled('search')) {
                $search = trim($request->search);
                $query->where(function($sub) use ($search) {
                    $sub->where('ten_thuoc', 'LIKE', "%{$search}%")
                        ->orWhere('hoat_chat', 'LIKE', "%{$search}%")
                        ->orWhere('ma_thuoc', 'LIKE', "%{$search}%");
                });
            }

            // 4. Lọc theo DANH MỤC (Category)
            if ($request->filled('category')) {
                $category = trim($request->category);
                if ($category !== 'Tất cả' && $category !== '') {
                    $query->where('danh_muc', $category);
                }
            }

            // 5. Lọc theo QUY CHẾ (Loại thuốc Rx/OTC)
            if ($request->filled('loai')) {
                $query->where('loai_thuoc', trim($request->loai));
            }

            // 6. Lọc theo KHOẢNG GIÁ (Price Range)
            if ($request->filled('min_price')) {
                $query->where('gia_ban', '>=', $request->min_price);
            }
            if ($request->filled('max_price')) {
                $query->where('gia_ban', '<=', $request->max_price);
            }

            // 7. Lọc theo TÌNH TRẠNG KHO (Stock status)
            if ($request->stock === 'sap_het') {
                $query->where('so_luong_ton', '<=', 10)->where('so_luong_ton', '>', 0);
            }

            // 8. Thực thi truy vấn, sắp xếp và phân trang
            // Sử dụng withQueryString() để giữ lại các filter trên link chuyển trang
            $danhSachThuoc = $query->orderBy('ten_thuoc', 'asc')
                                   ->paginate(16)
                                   ->withQueryString();

            // 9. Trả về View cùng dữ liệu
            return view('pos.index', compact('danhSachThuoc', 'dsDanhMuc'));

        } catch (\Exception $e) {
            Log::error("Lỗi POS Controller: " . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi tải dữ liệu.');
        }
    }

    /**
     * API lấy chi tiết 1 loại thuốc (Dùng cho AJAX thêm vào giỏ hàng)
     */
    public function show($id)
    {
        $thuoc = Thuoc::find($id);
        
        if (!$thuoc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thuốc này.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $thuoc
        ]);
    }

    /**
     * Hiển thị trang in hóa đơn dành riêng cho máy POS (Khổ 80mm)
     */
    public function printBill($id)
    {
        // Lấy hóa đơn kèm thông tin nhân viên và chi tiết thuốc
        $order = Order::with(['nhanVien', 'items.thuoc'])->findOrFail($id);
        
        return view('pos.print', compact('order'));
    }
}