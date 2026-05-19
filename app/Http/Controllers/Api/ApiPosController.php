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
   
   public function checkout(Request $request)
    {
        // 1. Kiểm tra đầu vào
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'cart'             => 'required|array|min:1',
            'cart.*.id'        => 'required|integer|exists:thuoc,id',
            'cart.*.quantity'  => 'required|integer|min:1',
            'cart.*.price'     => 'required|numeric|min:0',
            'total_amount'     => 'required|numeric|min:0',
            'note'             => 'nullable|string|max:500',
            'customer_id' => 'nullable|integer|exists:khach_hang,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ.', 'errors' => $validator->errors()], 422);
        }

        // 2. Định danh người dùng
        $isCustomer = auth('customer')->check();
        $isStaff = auth()->check();

        if (!$isCustomer && !$isStaff) {
            return response()->json(['status' => 'error', 'message' => 'Vui lòng đăng nhập.'], 401);
        }

        $khachHangId = null;
        $nhanVienId  = null;
        $status      = 'pending';
        $baseNote    = '';

        if ($isCustomer) {
            $khachHangId = auth('customer')->id(); 
            $nhanVienId  = 1; // Khách tự mua -> Gán cho Admin (Tránh lỗi null user_id)
            $status      = 'pending'; // Chờ duyệt
            $baseNote    = '[WEB] Đơn hàng đặt từ Website.';
        } else {
            $khachHangId = $request->customer_id ?? null;
            $nhanVienId  = auth()->id() ?? 1;
            $status      = 'completed'; // Tại quầy thì xong luôn
            $baseNote    = '[POS] Khách mua tại quầy.';
        }

        $finalNote = $request->filled('note') ? $baseNote . ' Ghi chú: ' . trim($request->note) : $baseNote;

        // 3. Xử lý Database Transaction
        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            $serverTotalAmount = 0;

            $order = \App\Models\Order::create([
                'customer_id' => $khachHangId, 
                'user_id'       => $nhanVienId,
                'total_price'   => 0, 
                'status'        => $status,
                'note'          => $finalNote
            ]);

            foreach ($request->cart as $item) {
                $thuoc = \App\Models\Thuoc::lockForUpdate()->find($item['id']);

                if (!$thuoc || $thuoc->trang_thai == 0) {
                    $tenSP = isset($item['name']) ? $item['name'] : $item['id'];
                    throw new \Exception("Sản phẩm [{$tenSP}] không khả dụng.");
                }

                if ($thuoc->so_luong_ton < $item['quantity']) {
                    throw new \Exception("Sản phẩm [{$thuoc->ten_thuoc}] chỉ còn {$thuoc->so_luong_ton} trong kho.");
                }

                $thanhTienItem = $item['quantity'] * $item['price'];
                $serverTotalAmount += $thanhTienItem;

                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'thuoc_id' => $thuoc->id,
                    'quantity' => $item['quantity'],
                    'price'    => $item['price'] 
                ]);

                $thuoc->decrement('so_luong_ton', $item['quantity']);
            }

            $order->update(['total_price' => $serverTotalAmount]);

            if ($khachHangId) {
                \App\Models\KhachHang::where('id', $khachHangId)->increment('diem_tich_luy', floor($serverTotalAmount / 1000));
            }

            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'status'   => 'success',
                'message'  => $isCustomer ? 'Đặt hàng thành công! Chúng tôi sẽ liên hệ sớm.' : 'Thanh toán hoàn tất!',
                'order_id' => str_pad($order->id, 4, '0', STR_PAD_LEFT)
            ], 200);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
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

    /**
     * Tìm kiếm khách hàng theo Tên hoặc SĐT (Dùng cho Combo Box POS)
     */
    public function searchCustomer(Request $request)
    {
        try {
            $q = trim($request->q);
            
            if (!$q) {
                return response()->json(['status' => 'success', 'data' => []]);
            }

            // Tìm theo SĐT, Tên hoặc Mã KH (chỉ lấy tài khoản đang hoạt động)
            $customers = \App\Models\KhachHang::where('trang_thai', 1)
                ->where(function($query) use ($q) {
                    $query->where('so_dien_thoai', 'LIKE', "%{$q}%")
                          ->orWhere('ten_khach_hang', 'LIKE', "%{$q}%")
                          ->orWhere('ma_khach_hang', 'LIKE', "%{$q}%");
                })
                ->take(10) // Lấy tối đa 10 người cho nhẹ máy
                ->get(['id', 'ma_khach_hang', 'ten_khach_hang', 'so_dien_thoai', 'diem_tich_luy']);

            return response()->json([
                'status' => 'success', 
                'data' => $customers
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi API searchCustomer: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'data' => []], 500);
        }
    }

    public function getMedicines(Request $request)
    {
        try {
            $query = Thuoc::where('trang_thai', 1);

            // Tìm kiếm tên thuốc, mã thuốc, hoạt chất
            if ($request->filled('search')) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('ten_thuoc', 'LIKE', "%{$search}%")
                    ->orWhere('ma_thuoc', 'LIKE', "%{$search}%")
                    ->orWhere('hoat_chat', 'LIKE', "%{$search}%");
                });
            }

            // Lọc danh mục
            if ($request->filled('category') && $request->category !== 'Tất cả') {
                $query->where('danh_muc', $request->category);
            }

            // Lọc giá bán
            if ($request->filled('min_price')) {
                $query->where('gia_ban', '>=', $request->min_price);
            }

            if ($request->filled('max_price')) {
                $query->where('gia_ban', '<=', $request->max_price);
            }

            // Lọc loại thuốc OTC / Rx
            if ($request->filled('loai')) {
                $query->where('loai_thuoc', $request->loai);
            }

            // Lọc tồn kho
            if ($request->filled('stock')) {
                if ($request->stock === 'sap_het') {
                    $query->where('so_luong_ton', '>', 0)
                        ->where('so_luong_ton', '<=', 10);
                }

                if ($request->stock === 'het_hang') {
                    $query->where('so_luong_ton', '<=', 0);
                }

                if ($request->stock === 'con_hang') {
                    $query->where('so_luong_ton', '>', 0);
                }
            }

            $medicines = $query
                ->orderBy('ten_thuoc')
                ->paginate(12)
                ->through(function ($thuoc) {
                    return [
                        // Dùng cho POS
                        'id'       => $thuoc->id,
                        'name'     => $thuoc->ten_thuoc,
                        'code'     => $thuoc->ma_thuoc,
                        'price'    => (int) $thuoc->gia_ban,
                        'stock'    => (int) $thuoc->so_luong_ton,
                        'unit'     => $thuoc->don_vi_tinh ?: $thuoc->don_vi_co_ban,
                        'image'    => $thuoc->hinh_anh ? asset($thuoc->hinh_anh) : null,

                        // Thông tin hiển thị / lọc
                        'active_ingredient' => $thuoc->hoat_chat,
                        'category'          => $thuoc->danh_muc,
                        'type'              => $thuoc->loai_thuoc,
                        'expiry_date'       => $thuoc->han_su_dung
                            ? $thuoc->han_su_dung->format('Y-m-d')
                            : null,

                        // Dùng cho phiếu nhập
                        'ten_thuoc'       => $thuoc->ten_thuoc,
                        'ma_thuoc'        => $thuoc->ma_thuoc,
                        'don_vi_co_ban'   => $thuoc->don_vi_co_ban,
                        'don_vi_nhap'     => $thuoc->don_vi_nhap ?: $thuoc->don_vi_co_ban,
                        'ty_le_quy_doi'   => (int) ($thuoc->ty_le_quy_doi ?: 1),
                        'gia_nhap'        => (int) $thuoc->gia_nhap,
                        'gia_ban'         => (int) $thuoc->gia_ban,
                        'so_luong_ton'    => (int) $thuoc->so_luong_ton,
                        'han_su_dung'     => $thuoc->han_su_dung
                            ? $thuoc->han_su_dung->format('Y-m-d')
                            : null,
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data'   => $medicines,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Lỗi API getMedicines: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Không thể tải danh sách thuốc.',
            ], 500);
        }
    }
}