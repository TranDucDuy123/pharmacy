<?php

namespace App\Http\Controllers;

use App\Models\Thuoc;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Storage; // Bỏ thư viện Storage mặc định
use Illuminate\Support\Facades\DB;
use Exception;

class AdminThuocController extends Controller
{
    /**
     * Danh sách thuốc kèm tìm kiếm và phân trang
     */
    public function index(Request $request)
    {
        $query = Thuoc::query();

        // Xử lý tìm kiếm (Tên, mã, hoạt chất)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ten_thuoc', 'LIKE', "%{$search}%")
                  ->orWhere('ma_thuoc', 'LIKE', "%{$search}%")
                  ->orWhere('hoat_chat', 'LIKE', "%{$search}%");
            });
        }

        // Lọc theo danh mục
        if ($request->filled('category')) {
            $query->where('danh_muc', $request->category);
        }

        $danhSachThuoc = $query->latest()->paginate(16);

        return view('admin.thuoc.index', compact('danhSachThuoc'));
    }

    /**
     * Form thêm mới
     */
    public function create()
    {
        return view('admin.thuoc.create');
    }

    /**
     * Xử lý lưu dữ liệu
     */
    public function store(Request $request)
    {
        // 1. Validation chuẩn tiếng Việt
        $request->validate([
            'ma_thuoc'      => 'required|unique:thuoc,ma_thuoc',
            'ten_thuoc'     => 'required|max:255',
            'don_vi_co_ban' => 'required|max:50',
            'don_vi_nhap'   => 'nullable|max:50', // Thêm validation cho đơn vị nhập
            'ty_le_quy_doi' => 'required|integer|min:1',
            'gia_nhap'      => 'required|numeric|min:0',
            'gia_ban'       => 'required|numeric|min:0',
            'so_luong_ton'  => 'required|integer|min:0',
            'han_su_dung'   => 'required|date',
            'hinh_anh'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'ma_thuoc.unique'    => 'Mã thuốc này đã tồn tại.',
            'ten_thuoc.required' => 'Vui lòng nhập tên thuốc.',
            'gia_nhap.numeric'   => 'Giá nhập phải là số.',
            'ty_le_quy_doi.min'  => 'Tỷ lệ quy đổi tối thiểu là 1.',
        ]);

        try {
            // Lấy tất cả dữ liệu NGOẠI TRỪ file hình ảnh để xử lý riêng
            $data = $request->except('hinh_anh');

            // 2. XỬ LÝ LOGIC QUY ĐỔI & ĐỒNG BỘ DB CŨ
            // Gán đơn vị tính cũ bằng đơn vị cơ bản để không bị lỗi NOT NULL
            $data['don_vi_tinh'] = $request->don_vi_co_ban;

            // Nếu tỷ lệ quy đổi là 1 hoặc không nhập Đơn vị nhập -> Ép về 1:1 chuẩn
            if (empty($data['don_vi_nhap']) || $data['ty_le_quy_doi'] <= 1) {
                $data['don_vi_nhap'] = $data['don_vi_co_ban'];
                $data['ty_le_quy_doi'] = 1;
            }

            // 3. XỬ LÝ UPLOAD ẢNH (TÊN FILE AN TOÀN)
            if ($request->hasFile('hinh_anh')) {
                $file = $request->file('hinh_anh');
                
                // Tạo tên file an toàn tuyệt đối (Timestamp + chuỗi ngẫu nhiên + đuôi file gốc)
                // Ví dụ: 1712345678_660b1a2b.jpg
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                // Chuyển file vào thư mục public/uploads/thuoc
                $file->move(public_path('uploads/thuoc'), $filename);
                
                // Lưu chuỗi URL chuẩn vào DB để sau này dùng hàm asset() gọi ra
                $data['hinh_anh'] = 'uploads/thuoc/' . $filename;
            }

            // 4. Đảm bảo trạng thái là boolean (0 hoặc 1)
            $data['trang_thai'] = $request->has('trang_thai') ? 1 : 0;

            // 5. Lưu vào Database
            Thuoc::create($data);

            return redirect()->route('thuoc.index')->with('thong_bao', 'Thêm mới thuốc thành công!');

        } catch (\Exception $e) { // Thêm dấu \ trước Exception để gọi class chuẩn của PHP
            return back()->withInput()->with('loi_he_thong', 'Lỗi khi lưu dữ liệu: ' . $e->getMessage());
        }
    }

    /**
     * Form chỉnh sửa
     */
    public function edit($id)
    {
        $thuoc = Thuoc::findOrFail($id);
        return view('admin.thuoc.edit', compact('thuoc'));
    }

    /**
     * Xử lý cập nhật
     */
   public function update(Request $request, $id)
    {
        $thuoc = Thuoc::findOrFail($id);

        // 1. Validation chuẩn tiếng Việt (Bổ sung đầy đủ các cột mới của Phase 2)
        $request->validate([
            'ma_thuoc'      => 'required|unique:thuoc,ma_thuoc,' . $id, // Bỏ qua id hiện tại khi check unique
            'ten_thuoc'     => 'required|max:255',
            'don_vi_co_ban' => 'required|max:50',
            'don_vi_nhap'   => 'nullable|max:50',
            'ty_le_quy_doi' => 'required|integer|min:1',
            'gia_nhap'      => 'required|numeric|min:0',
            'gia_ban'       => 'required|numeric|min:0',
            'so_luong_ton'  => 'required|integer|min:0',
            'han_su_dung'   => 'required|date',
            'hinh_anh'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'ma_thuoc.unique'    => 'Mã thuốc này đã tồn tại trong hệ thống.',
            'ten_thuoc.required' => 'Vui lòng nhập tên thuốc.',
            'gia_nhap.numeric'   => 'Giá nhập phải là định dạng số.',
            'ty_le_quy_doi.min'  => 'Tỷ lệ quy đổi tối thiểu là 1.',
        ]);

        try {
            // Lấy dữ liệu NGOẠI TRỪ hình ảnh
            $data = $request->except('hinh_anh');

            // 2. XỬ LÝ LOGIC QUY ĐỔI & ĐỒNG BỘ DB CŨ
            $data['don_vi_tinh'] = $request->don_vi_co_ban;

            // Nếu người dùng xóa trống đơn vị nhập hoặc set tỷ lệ = 1 -> Đưa về mặc định
            if (empty($data['don_vi_nhap']) || $data['ty_le_quy_doi'] <= 1) {
                $data['don_vi_nhap'] = $data['don_vi_co_ban'];
                $data['ty_le_quy_doi'] = 1;
            }

            // 3. XỬ LÝ ẢNH (XÓA CŨ + THÊM MỚI BẰNG TÊN AN TOÀN)
            if ($request->hasFile('hinh_anh')) {
                // Kiểm tra và xóa file ảnh cũ vật lý trên ổ cứng
                if (!empty($thuoc->hinh_anh) && file_exists(public_path($thuoc->hinh_anh))) {
                    unlink(public_path($thuoc->hinh_anh));
                }
                
                $file = $request->file('hinh_anh');
                // Mã hóa tên file mới giống hệt bên hàm store
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/thuoc'), $filename);
                $data['hinh_anh'] = 'uploads/thuoc/' . $filename;
            }

            // 4. Trạng thái kinh doanh
            $data['trang_thai'] = $request->boolean('trang_thai') ? 1 : 0;

            // 5. Cập nhật dữ liệu
            $thuoc->update($data);

            return redirect()->route('thuoc.index')->with('thong_bao', 'Cập nhật thông tin thuốc thành công!');

        } catch (\Exception $e) { // Bắt lỗi hệ thống chuẩn
            return back()->withInput()->with('loi_he_thong', 'Lỗi khi cập nhật: ' . $e->getMessage());
        }
    }

    /**
     * Xử lý xóa
     */
    public function destroy($id)
    {
        try {
            $thuoc = Thuoc::findOrFail($id);

            // Kiểm tra xem thuốc này đã có trong hóa đơn nào chưa
            $daBan = DB::table('order_items')->where('thuoc_id', $id)->exists();

            if ($daBan) {
                return back()->with('loi_he_thong', 'Không thể xóa thuốc đã có lịch sử giao dịch. Hãy chuyển trạng thái sang "Ngừng kinh doanh".');
            }

            // Xóa ảnh vật lý trực tiếp khỏi thư mục public trước khi xóa bản ghi
            if ($thuoc->hinh_anh && file_exists(public_path($thuoc->hinh_anh))) {
                unlink(public_path($thuoc->hinh_anh));
            }

            $thuoc->delete();

            return redirect()->route('thuoc.index')->with('thong_bao', 'Đã xóa thuốc khỏi hệ thống.');

        } catch (Exception $e) {
            return back()->with('loi_he_thong', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }
}