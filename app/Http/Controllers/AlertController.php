<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\ContractPaymentSchedule;
use Carbon\Carbon;

class AlertController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'all');

        $expiringContracts = collect();
        $unpaidPayments = collect();

        // Lấy hợp đồng sắp hết hạn trong 30 ngày tới
        if ($filter === 'all' || $filter === 'expiring') {
            $expiringContracts = Contract::with('kiosk', 'customer')
                ->where('status', 'active')
                ->whereBetween('end_date', [now(), now()->addDays(30)])
                ->orderBy('end_date', 'asc')
                ->get();
            
            // Tính số ngày còn lại (số nguyên)
            $expiringContracts->each(function ($contract) {
                $contract->days_remaining = (int) max(0, now()->startOfDay()->diffInDays(Carbon::parse($contract->end_date)->startOfDay()));
            });
        }

        // Lấy hóa đơn chậm thanh toán
        if ($filter === 'all' || $filter === 'unpaid') {
            $unpaidPayments = ContractPaymentSchedule::with('contract.kiosk', 'contract.customer')
                ->where('status', 'unpaid')
                ->where('due_date', '<', now())
                ->orderBy('due_date', 'asc')
                ->get();
            
            // Tính số ngày quá hạn (số nguyên)
            $unpaidPayments->each(function ($payment) {
                $payment->days_overdue = (int) max(0, Carbon::parse($payment->due_date)->startOfDay()->diffInDays(now()->startOfDay()));
            });
        }

        return view('admin.alerts.index', compact('expiringContracts', 'unpaidPayments', 'filter'));
    }
}
