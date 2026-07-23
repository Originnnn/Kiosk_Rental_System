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
            'contact_name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'business_type' => 'nullable|string|max:1000',
            'note' => 'nullable|string',
            'desired_start' => 'nullable|date',
            'desired_end' => 'nullable|date|after_or_equal:desired_start',
        ]);

        $duration_months = 12; // default
        if (!empty($validated['desired_start']) && !empty($validated['desired_end'])) {
            $start = \Carbon\Carbon::parse($validated['desired_start']);
            $end = \Carbon\Carbon::parse($validated['desired_end']);
            $duration_months = max(1, $start->diffInMonths($end));
        }

        $booking = BookingRequest::create([
            'kiosk_id' => $validated['kiosk_id'],
            'customer_name' => $validated['contact_name'],
            'phone' => $validated['contact_phone'],
            'email' => $validated['contact_email'] ?? null,
            'business_type' => $validated['business_type'] ?? 'Chưa xác định',
            'duration_months' => $duration_months,
            'notes' => $validated['note'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Gửi yêu cầu thành công, nhân viên bến xe sẽ sớm liên hệ lại với bạn.');
    }
}
