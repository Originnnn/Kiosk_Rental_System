<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Kiosk;
use App\Models\Contract;
use App\Models\ContractPaymentSchedule;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->query('period', 'this_month');
        $dwh = DB::connection('dwh');

        switch ($period) {
            case 'last_month':
                $startDate = now()->subMonth()->startOfMonth();
                $endDate = now()->subMonth()->endOfMonth();
                $prevStartDate = now()->subMonths(2)->startOfMonth();
                $prevEndDate = now()->subMonths(2)->endOfMonth();
                
                $periodLabel = "Tháng " . now()->subMonth()->month;
                $prevPeriodLabel = "tháng trước nữa";
                $groupType = 'day';
                break;
                
            case 'this_quarter':
                $startDate = now()->startOfQuarter();
                $endDate = now()->endOfQuarter();
                $prevStartDate = now()->subQuarter()->startOfQuarter();
                $prevEndDate = now()->subQuarter()->endOfQuarter();
                
                $periodLabel = "Quý " . now()->quarter;
                $prevPeriodLabel = "quý trước";
                $groupType = 'month';
                break;
                
            case 'last_quarter':
                $startDate = now()->subQuarter()->startOfQuarter();
                $endDate = now()->subQuarter()->endOfQuarter();
                $prevStartDate = now()->subQuarters(2)->startOfQuarter();
                $prevEndDate = now()->subQuarters(2)->endOfQuarter();
                
                $periodLabel = "Quý " . now()->subQuarter()->quarter;
                $prevPeriodLabel = "quý trước nữa";
                $groupType = 'month';
                break;

            case 'this_month':
            default:
                $period = 'this_month';
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                $prevStartDate = now()->subMonth()->startOfMonth();
                $prevEndDate = now()->subMonth()->endOfMonth();
                
                $periodLabel = "Tháng " . now()->month;
                $prevPeriodLabel = "tháng trước";
                $groupType = 'day';
                break;
        }

        // 1. TRUY VẤN KHO DỮ LIỆU (DWH) - DOANH THU KỲ NÀY
        $currentRevenueQuery = collect($dwh->select("
            SELECT SUM(f.TotalRevenue) as rev
            FROM `fact.rental` f
            JOIN `dim.date` d ON f.DateKey = d.DateKey
            WHERE d.FullDate BETWEEN ? AND ?
        ", [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]))->first();
        
        $currentRev = $currentRevenueQuery->rev ?? 0;

        // DOANH THU KỲ TRƯỚC
        $prevRevenueQuery = collect($dwh->select("
            SELECT SUM(f.TotalRevenue) as rev
            FROM `fact.rental` f
            JOIN `dim.date` d ON f.DateKey = d.DateKey
            WHERE d.FullDate BETWEEN ? AND ?
        ", [$prevStartDate->format('Y-m-d'), $prevEndDate->format('Y-m-d')]))->first();
        
        $prevRev = $prevRevenueQuery->rev ?? 0;
        $growth = $prevRev > 0 ? round((($currentRev - $prevRev) / $prevRev) * 100, 1) : ($currentRev > 0 ? 100 : 0);

        // BIỂU ĐỒ DOANH THU
        if ($groupType === 'month') {
            // Group theo tháng trong quý
            $barChartRaw = $dwh->select("
                SELECT d.Month, d.Year, SUM(f.TotalRevenue) as revenue
                FROM `fact.rental` f
                JOIN `dim.date` d ON f.DateKey = d.DateKey
                WHERE d.FullDate BETWEEN ? AND ?
                GROUP BY d.Year, d.Month
                ORDER BY d.Year ASC, d.Month ASC
            ", [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

            $barLabels = [];
            $barData = [];
            foreach ($barChartRaw as $row) {
                $barLabels[] = "T" . $row->Month;
                $barData[] = (float) $row->revenue;
            }
            $chartTitle = "Biểu đồ doanh thu (" . $periodLabel . ")";
        } else {
            // Group theo ngày trong tháng
            $barChartRaw = $dwh->select("
                SELECT d.FullDate, SUM(f.TotalRevenue) as revenue
                FROM `fact.rental` f
                JOIN `dim.date` d ON f.DateKey = d.DateKey
                WHERE d.FullDate BETWEEN ? AND ?
                GROUP BY d.FullDate
                ORDER BY d.FullDate ASC
            ", [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

            $barLabels = [];
            $barData = [];
            foreach ($barChartRaw as $row) {
                $barLabels[] = \Carbon\Carbon::parse($row->FullDate)->format('d/m');
                $barData[] = (float) $row->revenue;
            }
            $chartTitle = "Biểu đồ doanh thu (" . $periodLabel . ")";
        }

        // 2. TRUY VẤN VẬN HÀNH (OLTP)
        $totalKiosks = Kiosk::count();
        $rentedKiosks = Kiosk::where('status', 'rented')->count();
        $occupancyRate = $totalKiosks > 0 ? round(($rentedKiosks / $totalKiosks) * 100) : 0;

        // Số hợp đồng mới tạo trong kỳ
        $newContractsCount = Contract::whereBetween('created_at', [$startDate, $endDate])->count();
        $prevNewContractsCount = Contract::whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
        $contractsGrowth = $prevNewContractsCount > 0 
            ? round((($newContractsCount - $prevNewContractsCount) / $prevNewContractsCount) * 100, 1) 
            : ($newContractsCount > 0 ? 100 : 0);

        $activeContractsCount = Contract::where('status', 'active')->count();
        
        $expiringContracts = Contract::with('kiosk', 'customer')
            ->where('status', 'active')
            ->whereBetween('end_date', [now(), now()->addDays(30)])
            ->orderBy('end_date', 'asc')
            ->take(5)
            ->get();
        $expiringCountTotal = Contract::where('status', 'active')->whereBetween('end_date', [now(), now()->addDays(30)])->count();

        $unpaidPayments = ContractPaymentSchedule::with('contract.kiosk', 'contract.customer')
            ->where('status', 'unpaid')
            ->where('due_date', '<', now())
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'period', 'periodLabel', 'prevPeriodLabel',
            'currentRev', 'growth',
            'newContractsCount', 'contractsGrowth',
            'barLabels', 'barData', 'chartTitle',
            'totalKiosks', 'rentedKiosks', 'occupancyRate',
            'activeContractsCount', 'expiringContracts', 'expiringCountTotal',
            'unpaidPayments'
        ));
    }
}
