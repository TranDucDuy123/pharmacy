<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Thuoc;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; // Thêm Log để ghi nhận lỗi hệ thống

class ApiPosController extends Controller
{
    /**
     * Lấy danh sách thuốc với tìm kiếm và lọc nâng cao
     */
    public function getMedicines(Request $request)
    {
        try {
            $query = Thuoc::query()->where('trang_thai', 1);

            // Tìm kiếm đa năng (Tên, hoạt chất, mã thuốc)
            if ($request->filled('search')) {
                $s = trim($request->search);
                $query->where(function($q) use ($s) {
                    $q->where('ten_thuoc', 'LIKE', "%$s%")
                      ->orWhere('hoat_chat', 'LIKE', "%$s%")
                      ->orWhere('ma_thuoc', 'LIKE', "%$s%"); // Sửa thành LIKE cho mã thuốc để linh hoạt hơn
                });
            }

            // Lọc theo danh mục
            if ($request->filled('category') && $request->category !== 'Tất cả') {
                $query->where('danh_muc', trim($request->category));
            }

            // Lọc theo loại thuốc (Rx/OTC)
            if ($request->filled('loai')) {
                $query->where('loai_thuoc', trim($request->loai));
            }

            // Phân trang mặc định 20 mục mỗi trang
            $medicines = $query->orderBy('ten_thuoc', 'asc')->paginate(20);

            return response()->json([
                'status' => 'success',
                'data' => $medicines
            ], 200);

        } catch (\Exception $e) {
            Log::error('Lỗi API getMedicines: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi tải danh sách thuốc.'
            ], 500);
        }
    }

    /**
     * Xử lý thanh toán đơn hàng (Database Transaction)
     * Đảm bảo trừ kho và lưu hóa đơn diễn ra đồng thời
     */
    public function checkout(Request $request)
    {
        // 1. Kiểm tra tính hợp lệ của dữ liệu giỏ hàng gửi lên
        $validator = Validator::make($request->all(), [
            'cart'             => 'required|array|min:1',
            'cart.*.id'        => 'required|integer|exists:thuoc,id', // Thêm integer
            'cart.*.quantity'  => 'required|integer|min:1',
            'cart.*.price'     => 'required|numeric|min:0',
            'total_amount'     => 'required|numeric|min:0',
            'customer_id'      => 'nullable|integer', // Validate thêm customer_id nếu có
        ], [
            // Custom messages cho dễ hiểu
            'cart.required' => 'Giỏ hàng không được để trống.',
            'cart.*.id.exists' => 'Một hoặc nhiều loại thuốc không tồn tại trong hệ thống.',
            'total_amount.required' => 'Tổng tiền không hợp lệ.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Bắt đầu Transaction (Đảm bảo an toàn dữ liệu)
        DB::beginTransaction();

        try {
            // Lấy ID nhân viên đang thao tác
            $nhanVienId = auth()->check() ? auth()->id() : 1; 

            // Tính toán lại tổng tiền từ server để tránh việc user sửa data trên trình duyệt
            $serverTotalAmount = 0;

            // Bước 2.1: Tạo Hóa đơn gốc (Order)
            $order = Order::create([
                'customer_id' => $request->customer_id ?? null,
                'user_id'     => $nhanVienId,
                'total_price' => 0, // Tạm để 0, sẽ update sau khi tính toán xong chi tiết
                'status'      => 'completed',
                'note'        => 'Khách mua trực tiếp tại quầy (POS)'
            ]);

            // Bước 2.2: Lặp qua từng món trong giỏ hàng để lưu chi tiết và trừ kho
            foreach ($request->cart as $item) {
                // Dùng lockForUpdate() để khóa dòng này lại, tránh Race Condition
                $thuoc = Thuoc::lockForUpdate()->find($item['id']);

                if (!$thuoc) {
                     throw new \Exception("Thuốc có ID {$item['id']} không tồn tại hoặc đã bị xóa.");
                }

                // Kiểm tra trạng thái thuốc
                if ($thuoc->trang_thai == 0) {
                    throw new \Exception("Sản phẩm [{$thuoc->ten_thuoc}] đã ngừng kinh doanh.");
                }

                // Kiểm tra tồn kho
                if ($thuoc->so_luong_ton < $item['quantity']) {
                    throw new \Exception("Sản phẩm [{$thuoc->ten_thuoc}] chỉ còn {$thuoc->so_luong_ton} trong kho.");
                }

                $thanhTienItem = $item['quantity'] * $item['price'];
                $serverTotalAmount += $thanhTienItem;

                // Lưu chi tiết hóa đơn
                OrderItem::create([
                    'order_id' => $order->id,
                    'thuoc_id' => $thuoc->id,
                    'quantity' => $item['quantity'],
                    'price'    => $item['price'] 
                ]);

                // Trừ số lượng tồn kho
                $thuoc->decrement('so_luong_ton', $item['quantity']);
            }

            // Cập nhật lại tổng tiền chính xác
            $order->update(['total_price' => $serverTotalAmount]);

            // 3. Nếu mọi thứ suôn sẻ -> Lưu toàn bộ vào Database
            DB::commit();

            return response()->json([
                'status'   => 'success',
                'message'  => 'Thanh toán thành công!',
                'order_id' => str_pad($order->id, 4, '0', STR_PAD_LEFT)
            ], 200);

        } catch (\Exception $e) {
            // 4. Rollback và ghi Log nếu có lỗi
            DB::rollBack();
            Log::error('Lỗi thanh toán POS: ' . $e->getMessage(), ['cart' => $request->cart]);
            
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 400); // Bad Request cho các lỗi logic nghiệp vụ
        }
    }

    /**
     * Lấy danh sách danh mục động để hiển thị menu tag
     */
    public function getCategories()
    {
        try {
            $categories = Thuoc::select('danh_muc')
                ->distinct()
                ->whereNotNull('danh_muc')
                ->where('danh_muc', '!=', '')
                ->pluck('danh_muc');

            return response()->json([
                'status' => 'success',
                'data' => $categories
            ], 200);

        } catch (\Exception $e) {
             Log::error('Lỗi API getCategories: ' . $e->getMessage());
             return response()->json([
                'status' => 'error',
                'message' => 'Không thể tải danh mục.'
            ], 500);
        }
    }

    /**
     * Lấy danh sách toàn bộ đơn hàng (Chỉ dành cho Admin)
     */
    public function getOrders(Request $request)
    {
        // Lấy danh sách đơn hàng, lấy luôn thông tin nhân viên đã lập đơn (quan hệ user)
        $orders = Order::with('user') // Giả sử model Order của bạn có hàm user()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'data'   => $orders
        ], 200);
    }

    public function index(Request $request)
    {
        // 1. Dùng tên hàm relationship vừa tạo ở trên: 'nhanVien'
        $query = Order::with('nhanVien')->orderBy('created_at', 'desc');

        // 2. Xử lý tìm kiếm theo mã Hóa Đơn
        if ($request->filled('search')) {
            $searchId = ltrim($request->search, '0'); 
            $searchId = str_replace('ORD-', '', strtoupper($searchId)); 
            
            $query->where('id', 'LIKE', '%' . $searchId . '%');
        }

        // 3. Xử lý lọc theo ngày
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // 4. Phân trang
        $danhSachHoaDon = $query->paginate(15);

        return view('admin.orders.index', compact('danhSachHoaDon'));
    }

    public function createCustomer(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'so_dien_thoai'  => 'required|unique:khach_hang,so_dien_thoai|max:20',
            'ten_khach_hang' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        $lastKh = \App\Models\KhachHang::orderBy('id', 'desc')->first();
        $nextId = $lastKh ? $lastKh->id + 1 : 1;
        
        $khachHang = \App\Models\KhachHang::create([
            'ma_khach_hang'  => 'KH' . str_pad($nextId, 4, '0', STR_PAD_LEFT),
            'so_dien_thoai'  => $request->so_dien_thoai,
            'ten_khach_hang' => $request->ten_khach_hang,
            // Cấp mật khẩu mặc định chính là Số điện thoại của khách
            'password'       => \Illuminate\Support\Facades\Hash::make($request->so_dien_thoai),
            'diem_tich_luy'  => 0,
            'trang_thai'     => 1, // Kích hoạt hoạt động NGAY LẬP TỨC
        ]);

        return response()->json(['status' => 'success', 'data' => $khachHang]);
    }

}