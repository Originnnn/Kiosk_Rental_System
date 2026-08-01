<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kiosk;
use Illuminate\Support\Facades\File;

class KioskController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Kiosk::class);
        
        $query = Kiosk::query()->orderByRaw('LENGTH(code) asc, code asc');

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
        $this->authorize('create', Kiosk::class);
        
        $validated = $request->validate([
            'code' => 'required|string|unique:kiosks,code|max:50',
            'name' => 'required|string|max:255',
            'area' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'floor' => 'nullable|string|max:255',
            'kiosk_type' => 'nullable|string|max:255',
            'min_term' => 'nullable|string|max:255',
            'features' => 'nullable|array',
            'description' => 'nullable|string',
            'power_supply' => 'nullable|string',
            'water_supply' => 'nullable|string',
            'internet_connection' => 'nullable|string',
            'air_conditioning' => 'nullable|string',
        ]);

        $validated['status'] = 'available';

        Kiosk::create($validated);

        return redirect()->route('admin.kiosks.index')->with('success', 'Thêm mới Kiosk thành công!');
    }

    public function show($id)
    {
        $kiosk = Kiosk::with(['contracts' => function($q) {
            $q->orderBy('created_at', 'desc')->with('customer');
        }, 'images'])->findOrFail($id);
        
        $this->authorize('view', $kiosk);

        return response()->json($kiosk);
    }

    public function update(Request $request, $id)
    {
        // Reserved for future use if needed from Drawer
        $kiosk = Kiosk::findOrFail($id);
        $this->authorize('update', $kiosk);
        
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:kiosks,code,' . $kiosk->id,
            'name' => 'required|string|max:255',
            'area' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'floor' => 'nullable|string|max:255',
            'kiosk_type' => 'nullable|string|max:255',
            'min_term' => 'nullable|string|max:255',
            'features' => 'nullable|array',
            'description' => 'nullable|string',
            'power_supply' => 'nullable|string',
            'water_supply' => 'nullable|string',
            'internet_connection' => 'nullable|string',
            'air_conditioning' => 'nullable|string',
            'image_front' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image_angle' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image_closeup' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image_back' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $kioskData = \Illuminate\Support\Arr::except($validated, [
            'image_front', 'image_angle', 'image_closeup', 'image_back'
        ]);

        $kiosk->update($kioskData);

        $imageSlots = [
            'image_front' => ['alt_text' => 'Mặt tiền', 'sort_order' => 1, 'suffix' => 'mattien'],
            'image_angle' => ['alt_text' => 'Góc nghiêng', 'sort_order' => 2, 'suffix' => 'gocnghieng'],
            'image_closeup' => ['alt_text' => 'Cận cảnh', 'sort_order' => 3, 'suffix' => 'cancanh'],
            'image_back' => ['alt_text' => 'Mặt sau', 'sort_order' => 4, 'suffix' => 'matsau'],
        ];

        foreach ($imageSlots as $inputName => $meta) {
            if ($request->hasFile($inputName)) {
                $file = $request->file($inputName);
                $existingImage = $kiosk->images()->where('sort_order', $meta['sort_order'])->first();
                
                if ($existingImage && File::exists(public_path($existingImage->file_path))) {
                    File::delete(public_path($existingImage->file_path));
                }
                
                $kioskDir = strtolower($kiosk->code);
                $kioskPrefix = str_replace('-', '', $kioskDir);
                $destinationPath = public_path('uploads/kiosks/' . $kioskDir);
                
                if (!File::isDirectory($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true, true);
                }

                $orderPad = str_pad($meta['sort_order'], 2, '0', STR_PAD_LEFT);
                $extension = $file->getClientOriginalExtension();
                $filename = "{$kioskPrefix}_{$orderPad}_{$meta['suffix']}.{$extension}";
                
                $file->move($destinationPath, $filename);
                $filePath = 'uploads/kiosks/' . $kioskDir . '/' . $filename;
                
                if ($existingImage) {
                    $existingImage->update(['file_path' => $filePath, 'alt_text' => $meta['alt_text']]);
                } else {
                    $kiosk->images()->create([
                        'file_path' => $filePath,
                        'alt_text' => $meta['alt_text'],
                        'sort_order' => $meta['sort_order']
                    ]);
                }
            }
        }

        $kiosk->load(['contracts' => function($q) {
            $q->orderBy('created_at', 'desc')->with('customer');
        }, 'images']);

        return response()->json(['success' => true, 'message' => 'Cập nhật thành công', 'kiosk' => $kiosk]);
    }
}
