<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Thuoc;
use Illuminate\Support\Facades\Validator;

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
            $search = trim($request->search);
                $query->where(function($sub) use ($search) {
                    $sub->where('ten_thuoc', 'LIKE', "%{$search}%")
                        ->orWhere('hoat_chat', 'LIKE', "%{$search}%")
                        ->orWhere('ma_thuoc', 'LIKE', "%{$search}%")
                         // SỬA LẠI: TÌM KIẾM THEO DANH MỤC (Vì danh mục được đặt theo tên bệnh lý)
                        ->orWhere('danh_muc', 'LIKE', "%{$search}%");
                });
                  
        }

        if ($request->filled('category')) {
            $query->where('danh_muc', $request->category);
        }

        $medicines = $query->latest()->paginate(16);
        
        // Lấy danh sách danh mục để làm menu lọc
        $categories = Thuoc::where('trang_thai', 1)->whereNotNull('danh_muc')->distinct()->pluck('danh_muc');

        return view('storefront.index', compact('medicines', 'categories'));
    }

    public function checkout(Request $request)
    {
        // 1. Kiểm tra khách hàng đã đăng nhập chưa
        if (!auth('customer')->check()) {
            return response()->json(['status' => 'error', 'message' => 'Vui lòng đăng nhập để đặt hàng.'], 401);
        }

        // 2. Kiểm tra dữ liệu giỏ hàng
        $validator = Validator::make($request->all(), [
            'cart'             => 'required|array|min:1',
            'cart.*.id'        => 'required|exists:thuoc,id',
            'cart.*.quantity'  => 'required|integer|min:1',
            'total_amount'     => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Dữ liệu giỏ hàng không hợp lệ.'], 422);
        }

        // 3. Xử lý lưu đơn hàng
        DB::beginTransaction();
        try {
            $khachHangId = auth('customer')->id();

            // Tạo Hóa đơn (Status: pending - Chờ duyệt)
            $order = Order::create([
                'khach_hang_id' => $khachHangId,
                'user_id'       => null, // Trống vì khách tự đặt, không có nhân viên thao tác
                'total_price'   => $request->total_amount,
                'status'        => 'pending', 
                'note'          => 'Khách hàng đặt mua online trên Website'
            ]);

            // Thêm chi tiết và trừ tồn kho
            foreach ($request->cart as $item) {
                $thuoc = Thuoc::lockForUpdate()->find($item['id']);

                if ($thuoc->so_luong_ton < $item['quantity']) {
                    throw new \Exception("Rất tiếc, thuốc [{$thuoc->ten_thuoc}] chỉ còn {$thuoc->so_luong_ton} sản phẩm.");
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'thuoc_id' => $thuoc->id,
                    'quantity' => $item['quantity'],
                    'price'    => $item['price']
                ]);

                $thuoc->decrement('so_luong_ton', $item['quantity']);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Đặt hàng thành công! Nhà thuốc sẽ liên hệ bạn sớm nhất.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}