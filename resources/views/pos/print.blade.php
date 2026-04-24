<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In Hóa Đơn #{{ $order->id }}</title>
    
    <style>
        /* CSS thuần tối ưu cho máy in bill nhiệt 80mm */
        body {
            font-family: 'Courier New', Courier, monospace, sans-serif;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }
        .receipt-container {
            width: 100%;
            max-width: 300px; /* Phù hợp khổ giấy 80mm */
            margin: 0 auto;
            padding: 10px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .title { font-size: 18px; margin-bottom: 5px; }
        .divider { border-bottom: 1px dashed #000; margin: 10px 0; }
        
        table { w-full; width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th { border-bottom: 1px dashed #000; padding-bottom: 5px; text-align: left;}
        td { padding: 5px 0; vertical-align: top; }
        .item-name { display: block; font-weight: bold; margin-bottom: 2px; }
        
        .totals { margin-top: 10px; }
        .totals-row { display: flex; justify-content: space-between; margin-bottom: 3px; }
        .grand-total { font-size: 16px; font-weight: bold; margin-top: 5px; border-top: 1px solid #000; padding-top: 5px; }

        @media print {
            /* Ẩn các thứ thừa thãi khi in */
            @page { margin: 0; }
            body { margin: 0; }
        }
    </style>
</head>
<body>

    <div class="receipt-container">
        <!-- Header -->
        <div class="text-center">
            <div class="title font-bold">DYLY PHARMA</div>
            <div>ĐC: 123 Đường Y Tế, TP.HCM</div>
            <div>ĐT: 1900 9999</div>
        </div>

        <div class="divider"></div>

        <!-- Info -->
        <div>
            <div><span class="font-bold">HÓA ĐƠN BÁN LẺ</span></div>
            <div>Mã đơn: #ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</div>
            <div>Ngày lập: {{ $order->created_at->format('d/m/Y H:i') }}</div>
            <div>Thu ngân: {{ $order->nhanVien->ho_ten ?? 'Hệ thống' }}</div>
        </div>

        <div class="divider"></div>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th width="50%">Sản phẩm</th>
                    <th width="15%" class="text-center">SL</th>
                    <th width="35%" class="text-right">T.Tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        <span class="item-name">{{ $item->thuoc->ten_thuoc ?? 'Sản phẩm lỗi' }}</span>
                        <span style="font-size: 10px;">{{ number_format($item->price, 0, ',', '.') }}</span>
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right font-bold">{{ number_format($item->thanh_tien, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <div class="totals-row">
                <span>Tạm tính:</span>
                <span>{{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
            <div class="totals-row grand-total">
                <span>TỔNG CỘNG:</span>
                <span>{{ number_format($order->total_price, 0, ',', '.') }}₫</span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Footer -->
        <div class="text-center" style="font-size: 11px;">
            <p>Cảm ơn Quý Khách!</p>
            <p>Hàng mua rồi miễn đổi trả, trừ trường hợp lỗi từ NSX trong vòng 3 ngày.</p>
        </div>
    </div>

    <!-- Script Tự Động In và Đóng Tab -->
    <script>
        window.onload = function() {
            // Tự động gọi lệnh in của trình duyệt
            window.print();
        };

        // Sau khi người dùng in xong (hoặc bấm Hủy in), tự động đóng tab popup này lại
        window.onafterprint = function() {
            window.close();
        };
    </script>

</body>
</html>