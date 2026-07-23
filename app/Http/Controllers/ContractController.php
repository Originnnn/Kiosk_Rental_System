<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Kiosk;
use App\Models\ContractPaymentSchedule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        // ... Eager loading customer, kiosk
        $query = Contract::with(['customer', 'kiosk'])->orderBy('created_at', 'desc');
        
        if ($request->filled('q')) {
            $query->where('reference_code', 'like', '%' . $request->q . '%')
                  ->orWhereHas('customer', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->q . '%');
                  });
        }
        
        $contracts = $query->paginate(10);
        return view('admin.contracts.index', compact('contracts'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $kiosks = Kiosk::where('status', 'available')->orderBy('code')->get();
        return view('admin.contracts.create', compact('customers', 'kiosks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'required_without:customer_id|string|max:255',
            'customer_phone' => 'required_without:customer_id|string|max:20',
            'kiosk_id' => 'required|exists:kiosks,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'payment_cycle' => 'required|in:1,3,6,12', // Số tháng
            'deposit_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'manager_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $customerId = $request->input('customer_id');
            $contactName = $request->input('customer_name');
            $contactPhone = $request->input('customer_phone');
            
            if (!$customerId) {
                // Tự động tạo khách hàng mới
                $newCustomer = Customer::create([
                    'name' => $contactName,
                    'phone' => $contactPhone,
                    'email' => $contactPhone . '@noemail.local', // default dummy email since it's required in DB
                ]);
                $customerId = $newCustomer->id;
            } else {
                // Lấy thông tin từ khách hàng có sẵn nếu không nhập tay
                if (!$contactName || !$contactPhone) {
                    $existingCustomer = Customer::find($customerId);
                    if ($existingCustomer) {
                        $contactName = $contactName ?: $existingCustomer->name;
                        $contactPhone = $contactPhone ?: $existingCustomer->phone;
                    }
                }
            }

            $kiosk = Kiosk::findOrFail($validated['kiosk_id']);

            // 1. Tạo hợp đồng
            $contract = Contract::create([
                'reference_code' => 'HD-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                'kiosk_id' => $validated['kiosk_id'],
                'customer_id' => $customerId,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'status' => 'active',
                'total_amount' => $validated['total_amount'],
                'payment_cycle' => $validated['payment_cycle'] . ' tháng / lần',
                'deposit_amount' => $validated['deposit_amount'],
                'manager_name' => $validated['manager_name'] ?? null,
                'contact_name' => $contactName,
                'contact_phone' => $contactPhone,
                'notes' => $validated['notes'] ?? null,
            ]);

            // 2. Cập nhật Kiosk status
            $kiosk->update(['status' => 'rented']);

            // 3. Sinh các kỳ thanh toán (Payment Schedules)
            $startDate = Carbon::parse($validated['start_date']);
            $endDate = Carbon::parse($validated['end_date']);
            $cycleMonths = (int) $validated['payment_cycle'];
            $amountPerCycle = $kiosk->price * $cycleMonths;

            $currentDate = $startDate->copy();
            while ($currentDate->lt($endDate)) {
                $dueDate = $currentDate->copy(); // Ví dụ: ngày thanh toán là ngày bắt đầu của kỳ
                
                ContractPaymentSchedule::create([
                    'contract_id' => $contract->id,
                    'due_date' => $dueDate,
                    'amount' => $amountPerCycle,
                    'status' => 'unpaid'
                ]);

                $currentDate->addMonths($cycleMonths);
            }

            // 4. Cập nhật trạng thái Yêu cầu thuê nếu có
            if ($request->filled('booking_request_id')) {
                $bookingRequest = \App\Models\BookingRequest::find($request->input('booking_request_id'));
                if ($bookingRequest) {
                    $bookingRequest->update(['status' => 'resolved']);
                }
            }

            DB::commit();

            return redirect()->route('admin.contracts.index')->with('success', 'Tạo hợp đồng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $contract = Contract::with(['customer', 'kiosk', 'paymentSchedules' => function($q) {
            $q->orderBy('due_date', 'asc');
        }])->findOrFail($id);

        return view('admin.contracts.show', compact('contract'));
    }

    public function edit($id)
    {
        $contract = Contract::with(['customer', 'kiosk'])->findOrFail($id);
        return view('admin.contracts.edit', compact('contract'));
    }

    public function update(Request $request, $id)
    {
        $contract = Contract::findOrFail($id);

        $validated = $request->validate([
            'manager_name' => 'nullable|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            // File upload logic có thể thêm sau
        ]);

        $contract->update($validated);

        return redirect()->route('admin.contracts.show', $id)->with('success', 'Cập nhật hợp đồng thành công!');
    }
}
