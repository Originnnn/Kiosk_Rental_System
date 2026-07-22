<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function export()
    {
        // Tăng giới hạn bộ nhớ do DOMPDF tốn nhiều RAM để render báo cáo dữ liệu lớn
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', '120');

        $dwh = DB::connection('dwh');

        $data = $dwh->select("
            SELECT 
                d.Month, 
                d.Year, 
                k.Area,
                SUM(f.TotalRevenue) as Revenue
            FROM `Fact.Rental` f
            JOIN `Dim.Date` d ON f.DateKey = d.DateKey
            JOIN `Dim.Kiosk` k ON f.KioskKey = k.KioskKey
            GROUP BY d.Year, d.Month, k.Area
            ORDER BY d.Year DESC, d.Month DESC, k.Area ASC
        ");

        $pdf = Pdf::loadView('admin.reports.pdf', compact('data'));

        // Lưu file vào storage/app/public/reports
        $filename = 'report_' . date('mY_His') . '.pdf';
        $path = 'reports/' . $filename;
        Storage::disk('public')->put($path, $pdf->output());

        return response()->json([
            'status' => 'success',
            'url' => asset('storage/' . $path)
        ]);
    }
}
