<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContractPaymentSchedule;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', ContractPaymentSchedule::class);
        
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
                                
        $overdueAmount = ContractPaymentSchedule::where('status', 'unpaid')
                            ->where('due_date', '<', now()->startOfDay())
                            ->sum('amount');

        // Sort: Chưa thanh toán (bao gồm quá hạn) lên đầu, Đã thanh toán xuống cuối. Trong cùng nhóm thì ưu tiên ngày gần nhất.
        $payments = $query->orderByRaw("CASE WHEN status = 'paid' THEN 2 ELSE 1 END")
                          ->orderBy('due_date', 'asc')
                          ->paginate(10);

        return view('admin.payments.index', compact('payments', 'currentMonthRevenue', 'overdueAmount'));
    }

    public function showPaymentForm($id)
    {
        $payment = ContractPaymentSchedule::with(['contract.customer', 'contract.kiosk'])->findOrFail($id);
        $this->authorize('update', $payment);
        
        $recentPayments = ContractPaymentSchedule::where('contract_id', $payment->contract_id)
                            ->where('status', 'paid')
                            ->orderBy('paid_at', 'desc')
                            ->take(2)
                            ->get();

        return view('admin.payments.form', compact('payment', 'recentPayments'));
    }

    public function processPayment(Request $request, $id)
    {
        $schedule = ContractPaymentSchedule::findOrFail($id);
        $this->authorize('update', $schedule);
        
        $request->validate([
            'payment_date' => 'required|date',
            'actual_amount' => 'required|numeric',
            'payment_method' => 'required|string',
            'document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'note' => 'nullable|string'
        ]);
        
        $path = $schedule->receipt_file;
        if ($request->hasFile('document')) {
            $path = $request->file('document')->store('uploads/payments', 'public');
        }

        $schedule->transactions()->create([
            'amount' => $request->actual_amount,
            'payment_method' => $request->payment_method,
            'receipt_file' => $path,
            'notes' => $request->input('note'),
            'paid_at' => $request->payment_date,
        ]);

        $totalPaid = $schedule->transactions()->sum('amount');
        $remainingDebt = max(0, $schedule->amount - $totalPaid);
        
        $status = 'pending';
        if ($totalPaid > 0) {
            $status = $remainingDebt > 0 ? 'partial' : 'paid';
        }

        $schedule->update([
            'actual_amount' => $totalPaid,
            'remaining_debt' => $remainingDebt,
            'status' => $status,
            'paid_at' => $status == 'paid' ? $request->payment_date : $schedule->paid_at,
            'receipt_file' => $path,
            'notes' => $request->input('note'),
        ]);

        return redirect()->route('admin.payments.index')->with('success', 'Ghi nhận thanh toán thành công!');
    }
}
