<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Kiosk;
use App\Models\Contract;
use App\Models\ContractPaymentSchedule;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. TRUY VẤN KHO DỮ LIỆU (DWH)
        $dwh = DB::connection('dwh');
        
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $lastMonth = now()->subMonth()->month;
        $lastMonthYear = now()->subMonth()->year;

        $currentQuarter = ceil($currentMonth / 3);
        $lastQuarter = ceil($lastMonth / 3);
        $lastQuarterYear = now()->subMonths(3)->year;

        // Doanh thu tháng
        $monthlyRevenueQuery = $dwh->select("
            SELECT 
                SUM(CASE WHEN d.Month = ? AND d.Year = ? THEN f.TotalRevenue ELSE 0 END) as current_month_rev,
                SUM(CASE WHEN d.Month = ? AND d.Year = ? THEN f.TotalRevenue ELSE 0 END) as last_month_rev
            FROM `fact.rental` f
            JOIN `dim.date` d ON f.DateKey = d.DateKey
            WHERE d.Year IN (?, ?)
        ", [$currentMonth, $currentYear, $lastMonth, $lastMonthYear, $currentYear, $lastMonthYear]);
        
        $currentMonthRev = $monthlyRevenueQuery[0]->current_month_rev ?? 0;
        $lastMonthRev = $monthlyRevenueQuery[0]->last_month_rev ?? 0;
        $monthGrowth = $lastMonthRev > 0 ? round((($currentMonthRev - $lastMonthRev) / $lastMonthRev) * 100, 1) : 100;

        // Doanh thu quý
        $quarterlyRevenueQuery = $dwh->select("
            SELECT 
                SUM(CASE WHEN CEIL(d.Month / 3) = ? AND d.Year = ? THEN f.TotalRevenue ELSE 0 END) as current_quarter_rev,
                SUM(CASE WHEN CEIL(d.Month / 3) = ? AND d.Year = ? THEN f.TotalRevenue ELSE 0 END) as last_quarter_rev
            FROM `fact.rental` f
            JOIN `dim.date` d ON f.DateKey = d.DateKey
            WHERE d.Year IN (?, ?)
        ", [$currentQuarter, $currentYear, $lastQuarter, $lastQuarterYear, $currentYear, $lastQuarterYear]);

        $currentQuarterRev = $quarterlyRevenueQuery[0]->current_quarter_rev ?? 0;
        $lastQuarterRev = $quarterlyRevenueQuery[0]->last_quarter_rev ?? 0;
        $quarterGrowth = $lastQuarterRev > 0 ? round((($currentQuarterRev - $lastQuarterRev) / $lastQuarterRev) * 100, 1) : 100;

        // Biểu đồ doanh thu 6 tháng
        $barChartRaw = $dwh->select("
            SELECT d.Month, d.Year, SUM(f.TotalRevenue) as revenue
            FROM `fact.rental` f
            JOIN `dim.date` d ON f.DateKey = d.DateKey
            WHERE d.FullDate >= ?
            GROUP BY d.Year, d.Month
            ORDER BY d.Year DESC, d.Month DESC
            LIMIT 6
        ", [now()->subMonths(5)->startOfMonth()->format('Y-m-d')]);

        $barChartRaw = array_reverse($barChartRaw);
        
        $barLabels = [];
        $barData = [];
        foreach ($barChartRaw as $row) {
            $barLabels[] = "T" . $row->Month;
            $barData[] = (float) $row->revenue;
        }

        // 2. TRUY VẤN VẬN HÀNH (OLTP)
        $totalKiosks = Kiosk::count();
        $rentedKiosks = Kiosk::where('status', 'rented')->count();
        $occupancyRate = $totalKiosks > 0 ? round(($rentedKiosks / $totalKiosks) * 100) : 0;

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
            'currentMonthRev', 'monthGrowth',
            'currentQuarterRev', 'quarterGrowth',
            'barLabels', 'barData',
            'totalKiosks', 'rentedKiosks', 'occupancyRate',
            'activeContractsCount', 'expiringContracts', 'expiringCountTotal',
            'unpaidPayments'
        ));
    }
}
