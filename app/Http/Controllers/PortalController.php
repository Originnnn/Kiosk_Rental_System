<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use App\Models\Kiosk;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    public function index(Request $request)
    {
        $query = Kiosk::with(['position', 'images']);
        
        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('code', 'like', '%' . $request->q . '%')
                  ->orWhere('name', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->filled('zone')) {
            $query->whereHas('position', function($q) use ($request) {
                $q->where('zone', $request->zone);
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $kiosks = $query->get();
        
        return view('public.kiosks.index', compact('kiosks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kiosk_id' => 'required|exists:kiosks,id',
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'business_type' => 'nullable|string|max:1000',
            'duration_months' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $booking = BookingRequest::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Gửi yêu cầu thành công, nhân viên sẽ sớm liên hệ',
            'data' => $booking
        ], 201);
    }
}
