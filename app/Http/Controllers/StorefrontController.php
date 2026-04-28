<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Thuoc;

class StorefrontController extends Controller
{
    /**
     * Hiển thị trang chủ cửa hàng (Danh sách sản phẩm)
     */
    public function index(Request $request)
    {
        // Chỉ hiển thị thuốc đang kinh doanh và còn tồn kho
        $query = Thuoc::where('trang_thai', 1)->where('so_luong_ton', '>', 0);

        if ($request->filled('search')) {
            $query->where('ten_thuoc', 'LIKE', "%{$request->search}%")
                  ->orWhere('hoat_chat', 'LIKE', "%{$request->search}%");
        }

        if ($request->filled('category')) {
            $query->where('danh_muc', $request->category);
        }

        $medicines = $query->latest()->paginate(16);
        
        // Lấy danh sách danh mục để làm menu lọc
        $categories = Thuoc::where('trang_thai', 1)->whereNotNull('danh_muc')->distinct()->pluck('danh_muc');

        return view('storefront.index', compact('medicines', 'categories'));
    }
}