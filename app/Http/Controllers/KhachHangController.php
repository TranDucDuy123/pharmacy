<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KhachHang;
use Illuminate\Support\Facades\DB;

class KhachHangController extends Controller
{
    /**
     * Hiển thị danh sách khách hàng
     */
    public function index(Request $request)
    {
        $query = KhachHang::query();

        // Tìm kiếm theo tên hoặc SĐT
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ten_khach_hang', 'LIKE', "%{$search}%")
                  ->orWhere('so_dien_thoai', 'LIKE', "%{$search}%")
                  ->orWhere('ma_khach_hang', 'LIKE', "%{$search}%");
            });
        }

        $danhSachKhachHang = $query->latest()->paginate(15);
        
        // Thống kê nhanh
        $tongKhachHang = KhachHang::count();
        $khachHangMoiTrongThang = KhachHang::whereMonth('created_at', date('m'))->count();

        return view('admin.khach_hang.index', compact('danhSachKhachHang', 'tongKhachHang', 'khachHangMoiTrongThang'));
    }

    /**
     * Form thêm khách hàng mới
     */
    public function create()
    {
        return view('admin.khach_hang.create');
    }

    /**
     * Lưu khách hàng mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'ten_khach_hang' => 'required|max:255',
            'so_dien_thoai'  => 'required|unique:khach_hang,so_dien_thoai|max:20',
            'diem_tich_luy'  => 'nullable|integer|min:0',
        ], [
            'so_dien_thoai.unique' => 'Số điện thoại này đã được đăng ký.',
            'ten_khach_hang.required' => 'Vui lòng nhập tên khách hàng.',
        ]);

        try {
            $data = $request->all();
            
            // Tự động sinh mã khách hàng nếu không nhập
            if (empty($data['ma_khach_hang'])) {
                $lastKh = KhachHang::orderBy('id', 'desc')->first();
                $nextId = $lastKh ? $lastKh->id + 1 : 1;
                $data['ma_khach_hang'] = 'KH' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            }

            $data['trang_thai'] = $request->has('trang_thai') ? 1 : 0;
            $data['diem_tich_luy'] = $data['diem_tich_luy'] ?? 0;

            KhachHang::create($data);

            return redirect()->route('khach-hang.index')->with('thong_bao', 'Thêm khách hàng thành công!');
        } catch (\Exception $e) {
            return back()->withInput()->with('loi_he_thong', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Form chỉnh sửa khách hàng
     */
    public function edit($id)
    {
        $khachHang = KhachHang::findOrFail($id);
        return view('admin.khach_hang.edit', compact('khachHang'));
    }

    /**
     * Cập nhật thông tin
     */
    public function update(Request $request, $id)
    {
        $khachHang = KhachHang::findOrFail($id);

        $request->validate([
            'ten_khach_hang' => 'required|max:255',
            'so_dien_thoai'  => 'required|max:20|unique:khach_hang,so_dien_thoai,' . $id,
            'diem_tich_luy'  => 'required|integer|min:0',
        ]);

        try {
            $data = $request->all();
            $data['trang_thai'] = $request->boolean('trang_thai') ? 1 : 0;

            $khachHang->update($data);

            return redirect()->route('khach-hang.index')->with('thong_bao', 'Cập nhật khách hàng thành công!');
        } catch (\Exception $e) {
            return back()->withInput()->with('loi_he_thong', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Xóa khách hàng
     */
    public function destroy($id)
    {
        try {
            $khachHang = KhachHang::findOrFail($id);
            
            // Kiểm tra xem khách đã mua hàng chưa
            if (DB::table('orders')->where('customer_id', $id)->exists()) {
                return back()->with('loi_he_thong', 'Không thể xóa khách hàng đã có lịch sử mua hàng. Vui lòng chuyển trạng thái sang "Ngừng hoạt động".');
            }

            $khachHang->delete();
            return redirect()->route('khach-hang.index')->with('thong_bao', 'Đã xóa khách hàng khỏi hệ thống.');
        } catch (\Exception $e) {
            return back()->with('loi_he_thong', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }
}