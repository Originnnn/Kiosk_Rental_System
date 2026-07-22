<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContractPaymentSchedule;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = ContractPaymentSchedule::with(['contract.customer', 'contract.kiosk']);

        // Filter by text (Mã hợp đồng / Tên khách)
        if ($request->filled('q')) {
            $q = $request->q;
            $query->whereHas('contract', function($c) use ($q) {
                $c->where('reference_code', 'like', "%{$q}%")
                  ->orWhereHas('customer', function($cust) use ($q) {
                      $cust->where('name', 'like', "%{$q}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range (due_date) - Format: dd/mm/yyyy - dd/mm/yyyy
        if ($request->filled('date_range')) {
            $dates = explode(' - ', $request->date_range);
            if(count($dates) == 2) {
                try {
                    $start = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                    $end = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                    $query->whereBetween('due_date', [$start, $end]);
                } catch (\Exception $e) {
                    // Ignore parsing error
                }
            }
        }

        // KPIs
        $currentMonthRevenue = ContractPaymentSchedule::where('status', 'paid')
                                ->whereMonth('paid_at', now()->month)
                                ->whereYear('paid_at', now()->year)
                                ->sum(DB::raw('COALESCE(actual_amount, amount)'));
                                
        $overdueCount = ContractPaymentSchedule::where('status', 'unpaid')
                            ->where('due_date', '<', now()->startOfDay())
                            ->count();

        $payments = $query->orderBy('due_date', 'desc')->paginate(10);

        return view('admin.payments.index', compact('payments', 'currentMonthRevenue', 'overdueCount'));
    }

    public function showPaymentForm($id)
    {
        $payment = ContractPaymentSchedule::with(['contract.customer', 'contract.kiosk'])->findOrFail($id);
        
        $recentPayments = ContractPaymentSchedule::where('contract_id', $payment->contract_id)
                            ->where('status', 'paid')
                            ->orderBy('paid_at', 'desc')
                            ->take(2)
                            ->get();

        return view('admin.payments.form', compact('payment', 'recentPayments'));
    }

    public function processPayment(Request $request, $id)
    {
        $request->validate([
            'payment_date' => 'required|date',
            'actual_amount' => 'required|numeric',
            'payment_method' => 'required|string',
            'receipt_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'notes' => 'nullable|string'
        ]);

        $payment = ContractPaymentSchedule::findOrFail($id);
        
        $data = [
            'status' => 'paid',
            'paid_at' => $request->payment_date,
            'actual_amount' => $request->actual_amount,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
        ];

        if ($request->hasFile('receipt_file')) {
            $path = $request->file('receipt_file')->store('receipts', 'public');
            $data['receipt_file'] = $path;
        }

        $payment->update($data);

        return redirect()->route('admin.payments.index')->with('success', 'Ghi nhận thanh toán thành công!');
    }
}
