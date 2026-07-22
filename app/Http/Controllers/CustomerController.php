<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::withCount(['contracts as active_contracts_count' => function ($query) {
            $query->where('status', 'active');
        }])->orderBy('created_at', 'desc');

        if ($request->filled('q')) {
            $searchTerm = '%' . $request->q . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('phone', 'like', $searchTerm)
                  ->orWhere('id_card_number', 'like', $searchTerm)
                  ->orWhere('customer_code', 'like', $searchTerm);
            });
        }

        if ($request->filled('status') && $request->status !== 'Tất cả') {
            $statusMap = [
                'Hoạt động' => 'active',
                'Ngừng hoạt động' => 'inactive',
                'Chờ duyệt' => 'pending',
                'active' => 'active',
                'inactive' => 'inactive',
                'pending' => 'pending'
            ];
            
            $dbStatus = $statusMap[$request->status] ?? $request->status;
            $query->where('status', $dbStatus);
        }

        $customers = $query->paginate(10);
        return view('admin.customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10', 'unique:customers,phone'],
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'id_card_number' => 'required|string|unique:customers,id_card_number',
            'id_card_front' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // 5MB
            'id_card_back' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ], [
            'phone.regex' => 'Số điện thoại không hợp lệ (cần 10 số bắt đầu bằng 0).',
            'phone.unique' => 'Số điện thoại đã tồn tại trong hệ thống.',
            'id_card_number.unique' => 'Số CCCD/CMND đã tồn tại trong hệ thống.',
            'id_card_front.image' => 'File mặt trước CCCD phải là hình ảnh.',
            'id_card_back.image' => 'File mặt sau CCCD phải là hình ảnh.',
        ]);

        $data = [
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'address' => $validated['address'],
            'id_card_number' => $validated['id_card_number'],
            'status' => 'active',
        ];

        if ($request->hasFile('id_card_front')) {
            $path = $request->file('id_card_front')->store('customers/id_cards', 'public');
            $data['id_card_front'] = $path;
        }

        if ($request->hasFile('id_card_back')) {
            $path = $request->file('id_card_back')->store('customers/id_cards', 'public');
            $data['id_card_back'] = $path;
        }

        Customer::create($data);

        return redirect()->route('admin.customers.index')->with('success', 'Thêm mới khách thuê thành công!');
    }

    public function show($id)
    {
        $customer = Customer::with(['contracts.kiosk'])->findOrFail($id);
        return view('admin.customers.show', compact('customer'));
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10', 'unique:customers,phone,' . $customer->id],
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'id_card_number' => 'required|string|unique:customers,id_card_number,' . $customer->id,
            'id_card_front' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'id_card_back' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $data = [
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'address' => $validated['address'],
            'id_card_number' => $validated['id_card_number'],
        ];

        if ($request->hasFile('id_card_front')) {
            // Delete old file if exists
            if ($customer->id_card_front) {
                Storage::disk('public')->delete($customer->id_card_front);
            }
            $data['id_card_front'] = $request->file('id_card_front')->store('customers/id_cards', 'public');
        }

        if ($request->hasFile('id_card_back')) {
            // Delete old file if exists
            if ($customer->id_card_back) {
                Storage::disk('public')->delete($customer->id_card_back);
            }
            $data['id_card_back'] = $request->file('id_card_back')->store('customers/id_cards', 'public');
        }

        $customer->update($data);

        return redirect()->route('admin.customers.show', $customer->id)->with('success', 'Cập nhật thông tin thành công!');
    }


    public function toggleStatus($id)
    {
        $customer = Customer::findOrFail($id);
        
        $customer->status = $customer->status === 'active' ? 'inactive' : 'active';
        $customer->save();

        return redirect()->back()->with('success', 'Thay đổi trạng thái thành công!');
    }
}
