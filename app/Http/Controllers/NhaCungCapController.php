<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NhaCungCap;
use Illuminate\Support\Facades\DB;

class NhaCungCapController extends Controller
{
    /**
     * Danh sách Nhà cung cấp
     */
    public function index(Request $request)
    {
        $query = NhaCungCap::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ten_ncc', 'LIKE', "%{$search}%")
                  ->orWhere('so_dien_thoai', 'LIKE', "%{$search}%")
                  ->orWhere('ma_ncc', 'LIKE', "%{$search}%");
            });
        }

        $danhSachNCC = $query->latest()->paginate(15);
        $tongNCC = NhaCungCap::count();

        return view('admin.nha_cung_cap.index', compact('danhSachNCC', 'tongNCC'));
    }

    /**
     * Form thêm mới
     */
    public function create()
    {
        return view('admin.nha_cung_cap.create');
    }

    /**
     * Lưu dữ liệu thêm mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'ten_ncc' => 'required|max:255',
            'so_dien_thoai' => 'nullable|max:20',
            'email' => 'nullable|email|max:255',
            'ma_ncc' => 'nullable|unique:nha_cung_cap,ma_ncc|max:50',
        ]);

        try {
            $data = $request->all();
            
            // Tự tạo mã nếu trống
            if (empty($data['ma_ncc'])) {
                $last = NhaCungCap::orderBy('id', 'desc')->first();
                $nextId = $last ? $last->id + 1 : 1;
                $data['ma_ncc'] = 'NCC' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
            }

            $data['trang_thai'] = $request->has('trang_thai') ? 1 : 0;

            NhaCungCap::create($data);

            return redirect()->route('nha-cung-cap.index')->with('thong_bao', 'Thêm nhà cung cấp thành công!');
        } catch (\Exception $e) {
            return back()->withInput()->with('loi_he_thong', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Form chỉnh sửa
     */
    public function edit($id)
    {
        $nhaCungCap = NhaCungCap::findOrFail($id);
        return view('admin.nha_cung_cap.edit', compact('nhaCungCap'));
    }

    /**
     * Lưu cập nhật
     */
    public function update(Request $request, $id)
    {
        $nhaCungCap = NhaCungCap::findOrFail($id);

        $request->validate([
            'ten_ncc' => 'required|max:255',
            'so_dien_thoai' => 'nullable|max:20',
            'email' => 'nullable|email|max:255',
            'ma_ncc' => 'required|max:50|unique:nha_cung_cap,ma_ncc,' . $id,
        ]);

        try {
            $data = $request->all();
            $data['trang_thai'] = $request->boolean('trang_thai') ? 1 : 0;

            $nhaCungCap->update($data);

            return redirect()->route('nha-cung-cap.index')->with('thong_bao', 'Cập nhật nhà cung cấp thành công!');
        } catch (\Exception $e) {
            return back()->withInput()->with('loi_he_thong', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Xóa nhà cung cấp
     */
    public function destroy($id)
    {
        try {
            $nhaCungCap = NhaCungCap::findOrFail($id);
            
            // Chặn xóa nếu đã có phiếu nhập hàng từ nhà cung cấp này
            if (DB::table('phieu_nhap')->where('nha_cung_cap_id', $id)->exists()) {
                return back()->with('loi_he_thong', 'Không thể xóa nhà cung cấp đã có giao dịch nhập kho. Vui lòng chuyển trạng thái sang "Ngừng hợp tác".');
            }

            $nhaCungCap->delete();
            return redirect()->route('nha-cung-cap.index')->with('thong_bao', 'Đã xóa nhà cung cấp khỏi hệ thống.');
        } catch (\Exception $e) {
            return back()->with('loi_he_thong', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }
}