<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo doanh thu Kiosk</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #1e3a8a;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        .table th {
            background-color: #f3f4f6;
            color: #111827;
        }
        .table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right !important;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BÁO CÁO DOANH THU KIOSK</h1>
        <p>Phân loại theo khu vực và thời gian</p>
        <p><i>Ngày xuất: {{ date('d/m/Y H:i:s') }}</i></p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Tháng / Năm</th>
                <th>Khu vực (m2)</th>
                <th>Tổng Doanh Thu (VNĐ)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAll = 0;
            @endphp
            @forelse($data as $row)
                @php
                    $totalAll += $row->Revenue;
                @endphp
                <tr>
                    <td>{{ str_pad($row->Month, 2, '0', STR_PAD_LEFT) }} / {{ $row->Year }}</td>
                    <td>Khu vực {{ floatval($row->Area) }} m&sup2;</td>
                    <td class="text-right">{{ number_format($row->Revenue, 0, ',', '.') }} đ</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Không có dữ liệu</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-right">TỔNG CỘNG:</th>
                <th class="text-right">{{ number_format($totalAll, 0, ',', '.') }} đ</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
