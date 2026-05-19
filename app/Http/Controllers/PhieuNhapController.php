<?php

namespace App\Http\Controllers;

use App\Models\PhieuNhap;
use App\Models\ChiTietPhieuNhap;
use App\Models\NhaCungCap;
use App\Models\Thuoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PhieuNhapController extends Controller
{
    public function index(Request $request)
    {
        $query = PhieuNhap::with(['nhaCungCap', 'nguoiLap'])->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('ma_phieu', 'LIKE', "%{$search}%")
                  ->orWhereHas('nhaCungCap', function ($ncc) use ($search) {
                      $ncc->where('ten_ncc', 'LIKE', "%{$search}%")
                          ->orWhere('ma_ncc', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        $danhSachPhieuNhap = $query->paginate(15);

        $tongTienNhapThang = PhieuNhap::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('trang_thai', 'completed')
            ->sum('tong_tien_nhap');

        return view('admin.phieu_nhap.index', compact('danhSachPhieuNhap', 'tongTienNhapThang'));
    }

    public function create()
    {
        $nhaCungCaps = NhaCungCap::where('trang_thai', 1)
            ->orderBy('ten_ncc')
            ->get();

        $thuocs = Thuoc::where('trang_thai', 1)
            ->orderBy('ten_thuoc')
            ->get([
                'id',
                'ma_thuoc',
                'ten_thuoc',
                'don_vi_nhap',
                'don_vi_co_ban',
                'ty_le_quy_doi',
                'gia_nhap',
                'han_su_dung',
                'so_luong_ton',
            ]);

        return view('admin.phieu_nhap.create', compact('nhaCungCaps', 'thuocs'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nha_cung_cap_id' => 'required|exists:nha_cung_cap,id',
            'items' => 'required|array|min:1',
            'items.*.thuoc_id' => 'required|exists:thuoc,id',
            'items.*.so_luong_nhap' => 'required|integer|min:1',
            'items.*.gia_nhap' => 'required|numeric|min:0',
            'items.*.han_su_dung' => 'required|date',
        ], [
            'nha_cung_cap_id.required' => 'Vui lòng chọn nhà cung cấp.',
            'nha_cung_cap_id.exists' => 'Nhà cung cấp không tồn tại.',
            'items.required' => 'Phiếu nhập phải có ít nhất 1 mặt hàng.',
            'items.array' => 'Danh sách thuốc nhập không hợp lệ.',
            'items.min' => 'Phiếu nhập phải có ít nhất 1 mặt hàng.',
            'items.*.thuoc_id.required' => 'Vui lòng chọn thuốc.',
            'items.*.thuoc_id.exists' => 'Thuốc được chọn không tồn tại.',
            'items.*.so_luong_nhap.required' => 'Vui lòng nhập số lượng nhập.',
            'items.*.so_luong_nhap.integer' => 'Số lượng nhập phải là số nguyên.',
            'items.*.so_luong_nhap.min' => 'Số lượng nhập tối thiểu là 1.',
            'items.*.gia_nhap.required' => 'Vui lòng nhập giá nhập.',
            'items.*.gia_nhap.numeric' => 'Giá nhập phải là số.',
            'items.*.gia_nhap.min' => 'Giá nhập không được âm.',
            'items.*.han_su_dung.required' => 'Vui lòng nhập hạn sử dụng.',
            'items.*.han_su_dung.date' => 'Hạn sử dụng không hợp lệ.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            $phieuNhap = PhieuNhap::create([
                'ma_phieu' => 'PN' . Carbon::now()->format('YmdHis'),
                'nha_cung_cap_id' => $request->nha_cung_cap_id,
                'nhan_vien_id' => Auth::id() ?? 1,
                'tong_tien_nhap' => 0,
                'trang_thai' => 'completed',
                'ghi_chu' => $request->ghi_chu,
            ]);

            $tongTien = 0;

            foreach ($request->items as $item) {
                $thuoc = Thuoc::findOrFail($item['thuoc_id']);

                $tyLe = isset($item['ty_le_quy_doi']) && (int) $item['ty_le_quy_doi'] > 0
                    ? (int) $item['ty_le_quy_doi']
                    : ((int) $thuoc->ty_le_quy_doi > 0 ? (int) $thuoc->ty_le_quy_doi : 1);

                $soLuongNhap = (int) $item['so_luong_nhap'];
                $giaNhap = (int) $item['gia_nhap'];

                $soLuongCoBan = $soLuongNhap * $tyLe;
                $thanhTien = $soLuongNhap * $giaNhap;

                ChiTietPhieuNhap::create([
                    'phieu_nhap_id' => $phieuNhap->id,
                    'thuoc_id' => $thuoc->id,
                    'don_vi_nhap' => $item['don_vi_nhap'] ?? $thuoc->don_vi_nhap,
                    'so_luong_nhap' => $soLuongNhap,
                    'gia_nhap' => $giaNhap,
                    'ty_le_quy_doi' => $tyLe,
                    'so_luong_co_ban' => $soLuongCoBan,
                    'thanh_tien' => $thanhTien,
                    'han_su_dung_moi' => $item['han_su_dung'],
                ]);

                $thuoc->increment('so_luong_ton', $soLuongCoBan);

                $thuoc->update([
                    'han_su_dung' => $item['han_su_dung'],
                    'gia_nhap' => floor($giaNhap / $tyLe),
                ]);

                $tongTien += $thanhTien;
            }

            $phieuNhap->update([
                'tong_tien_nhap' => $tongTien,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lập phiếu nhập và cộng tồn kho thành công!',
                'redirect' => route('phieu-nhap.index'),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Lỗi tạo phiếu nhập: ' . $e->getMessage(), [
                'payload' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $phieuNhap = PhieuNhap::with(['chiTiet.thuoc', 'nhaCungCap', 'nguoiLap'])
            ->findOrFail($id);

        return view('admin.phieu_nhap.show', compact('phieuNhap'));
    }

    public function complete(PhieuNhap $phieuNhap)
    {
        if ($phieuNhap->trang_thai !== 'pending') {
            return back()->with('loi_he_thong', 'Chỉ có thể hoàn tất phiếu nhập đang ở trạng thái nháp.');
        }

        try {
            DB::beginTransaction();

            $phieuNhap->load('chiTiet');

            foreach ($phieuNhap->chiTiet as $chiTiet) {
                $thuoc = Thuoc::lockForUpdate()->findOrFail($chiTiet->thuoc_id);

                $thuoc->increment('so_luong_ton', (int) $chiTiet->so_luong_co_ban);

                $tyLe = max((int) ($chiTiet->ty_le_quy_doi ?? 1), 1);

                $thuoc->update([
                    'han_su_dung' => $chiTiet->han_su_dung_moi,
                    'gia_nhap' => floor($chiTiet->gia_nhap / $tyLe),
                ]);
            }

            $phieuNhap->update([
                'trang_thai' => 'completed',
            ]);

            DB::commit();

            return redirect()
                ->route('phieu-nhap.show', $phieuNhap->id)
                ->with('thong_bao', 'Đã hoàn tất phiếu nhập và cộng tồn kho thành công.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('loi_he_thong', 'Lỗi khi hoàn tất phiếu nhập: ' . $e->getMessage());
        }
    }

    public function cancel(PhieuNhap $phieuNhap)
    {
        if ($phieuNhap->trang_thai !== 'pending') {
            return back()->with('loi_he_thong', 'Chỉ có thể hủy phiếu nhập đang ở trạng thái nháp.');
        }

        try {
            $phieuNhap->update([
                'trang_thai' => 'cancelled',
            ]);

            return redirect()
                ->route('phieu-nhap.show', $phieuNhap->id)
                ->with('thong_bao', 'Đã hủy phiếu nhập.');

        } catch (\Exception $e) {
            return back()->with('loi_he_thong', 'Lỗi khi hủy phiếu nhập: ' . $e->getMessage());
        }
    }

    private function generateMaPhieu(): string
    {
        $prefix = 'PN' . now()->format('Ymd');

        $last = PhieuNhap::where('ma_phieu', 'LIKE', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        if (!$last) {
            return $prefix . '001';
        }

        $number = (int) substr($last->ma_phieu, -3);

        return $prefix . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
    }
}