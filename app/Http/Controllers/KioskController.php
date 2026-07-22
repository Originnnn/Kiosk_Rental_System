<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kiosk;

class KioskController extends Controller
{
    public function index(Request $request)
    {
        $query = Kiosk::query();

        if ($request->filled('q')) {
            $searchTerm = '%' . $request->q . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('code', 'like', $searchTerm)
                  ->orWhere('name', 'like', $searchTerm);
            });
        }

        if ($request->filled('status') && $request->status !== 'Tất cả trạng thái') {
            $statusMap = [
                'Trống' => 'available',
                'Đang thuê' => 'rented',
                'Đã đặt' => 'reserved',
                'Bảo trì' => 'maintenance',
                'available' => 'available',
                'rented' => 'rented',
                'reserved' => 'reserved',
                'maintenance' => 'maintenance'
            ];
            
            $dbStatus = $statusMap[$request->status] ?? $request->status;
            $query->where('status', $dbStatus);
        }

        $kiosks = $query->paginate(10);
        return view('admin.kiosks.index', compact('kiosks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:kiosks,code|max:50',
            'name' => 'required|string|max:255',
            'area' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $validated['status'] = 'available';

        Kiosk::create($validated);

        return redirect()->route('admin.kiosks.index')->with('success', 'Thêm mới Kiosk thành công!');
    }

    public function show($id)
    {
        $kiosk = Kiosk::with(['contracts' => function($q) {
            $q->orderBy('created_at', 'desc')->with('customer');
        }])->findOrFail($id);

        return response()->json($kiosk);
    }

    public function update(Request $request, $id)
    {
        // Reserved for future use if needed from Drawer
        $kiosk = Kiosk::findOrFail($id);
        
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:kiosks,code,' . $kiosk->id,
            'name' => 'required|string|max:255',
            'area' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $kiosk->update($validated);

        return response()->json(['success' => true, 'message' => 'Cập nhật thành công', 'kiosk' => $kiosk]);
    }
}
