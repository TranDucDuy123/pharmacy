<?php

namespace App\Http\Controllers; 

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PhieuNhap;
use App\Models\ChiTietPhieuNhap;
use App\Models\NhaCungCap;
use App\Models\Thuoc;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PhieuNhapController extends Controller
{
    /**
     * Danh sách Phiếu Nhập
     */
    public function index(Request $request)
    {
        $query = PhieuNhap::with(['nhaCungCap', 'nguoiLap'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('ma_phieu', 'LIKE', "%{$search}%");
        }

        $danhSachPhieuNhap = $query->paginate(15);
        
        $tongTienNhapThang = PhieuNhap::whereMonth('created_at', date('m'))
                                      ->where('trang_thai', 'completed')
                                      ->sum('tong_tien_nhap');

        return view('admin.phieu_nhap.index', compact('danhSachPhieuNhap', 'tongTienNhapThang'));
    }

    /**
     * Màn hình tạo Phiếu Nhập (Tương tác Alpine.js)
     */
    public function create()
    {
        $nhaCungCaps = NhaCungCap::where('trang_thai', 1)->get();
        
        // Lấy danh sách thuốc (chỉ lấy các trường cần thiết để nhúng vào Javascript)
        $thuocs = Thuoc::where('trang_thai', 1)->select('id', 'ma_thuoc', 'ten_thuoc', 'don_vi_nhap', 'don_vi_co_ban', 'ty_le_quy_doi', 'gia_nhap', 'han_su_dung')->get();

        return view('admin.phieu_nhap.create', compact('nhaCungCaps', 'thuocs'));
    }

    /**
     * Xử lý lưu Phiếu Nhập & Cộng tồn kho (Transaction)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nha_cung_cap_id' => 'required|exists:nha_cung_cap,id',
            'items'           => 'required|array|min:1',
            'items.*.thuoc_id'      => 'required|exists:thuoc,id',
            'items.*.so_luong_nhap' => 'required|integer|min:1',
            'items.*.gia_nhap'      => 'required|numeric|min:0',
            'items.*.han_su_dung'   => 'required|date',
        ], [
            'items.required' => 'Phiếu nhập phải có ít nhất 1 mặt hàng.',
        ]);

        try {
            DB::beginTransaction();

            // 1. Tạo Phiếu Nhập gốc
            $phieuNhap = PhieuNhap::create([
                'ma_phieu'        => 'PN' . Carbon::now()->format('YmdHis'),
                'nha_cung_cap_id' => $request->nha_cung_cap_id,
                'nhan_vien_id'    => Auth::id() ?? 1, // Tạm thời để 1 nếu chưa đăng nhập chuẩn
                'tong_tien_nhap'  => 0, // Sẽ tính lại sau khi lặp chi tiết
                'trang_thai'      => 'completed',
                'ghi_chu'         => $request->ghi_chu
            ]);

            $tongTien = 0;

            // 2. Lưu Chi tiết & Cộng Tồn kho
            foreach ($request->items as $item) {
                $thuoc = Thuoc::find($item['thuoc_id']);
                
                // Thu thập số liệu quy đổi
                $tyLe = $item['ty_le_quy_doi'] ?? $thuoc->ty_le_quy_doi;
                $soLuongCoBan = $item['so_luong_nhap'] * $tyLe;
                $thanhTien = $item['so_luong_nhap'] * $item['gia_nhap'];

                // Lưu dòng chi tiết phiếu nhập
                ChiTietPhieuNhap::create([
                    'phieu_nhap_id'   => $phieuNhap->id,
                    'thuoc_id'        => $thuoc->id,
                    'don_vi_nhap'     => $item['don_vi_nhap'] ?? $thuoc->don_vi_nhap,
                    'so_luong_nhap'   => $item['so_luong_nhap'],
                    'gia_nhap'        => $item['gia_nhap'],
                    'ty_le_quy_doi'   => $tyLe,
                    'so_luong_co_ban' => $soLuongCoBan,
                    'thanh_tien'      => $thanhTien,
                    'han_su_dung_moi' => $item['han_su_dung'],
                ]);

                // 3. CỘNG VÀO KHO BẢNG THUỐC
                $thuoc->increment('so_luong_ton', $soLuongCoBan);
                
                // 4. Cập nhật HSD và Giá nhập mới cho thuốc (Cập nhật lô mới nhất)
                $thuoc->update([
                    'han_su_dung' => $item['han_su_dung'],
                    // Nếu giá nhập 1 hộp là 100k, tỷ lệ là 100 -> Giá nhập 1 viên = 1000đ
                    'gia_nhap'    => floor($item['gia_nhap'] / $tyLe)
                ]);

                $tongTien += $thanhTien;
            }

            // 5. Cập nhật tổng tiền cho Phiếu
            $phieuNhap->update(['tong_tien_nhap' => $tongTien]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Lập phiếu nhập và cộng tồn kho thành công!',
                'redirect' => route('phieu-nhap.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xem chi tiết Phiếu Nhập
     */
    public function show($id)
    {
        $phieuNhap = PhieuNhap::with(['chiTiet.thuoc', 'nhaCungCap', 'nguoiLap'])->findOrFail($id);
        return view('admin.phieu_nhap.show', compact('phieuNhap'));
    }
}