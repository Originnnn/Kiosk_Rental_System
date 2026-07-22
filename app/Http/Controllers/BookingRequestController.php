<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use Illuminate\Http\Request;

class BookingRequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kiosk_id' => 'required|exists:kiosks,id',
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'business_type' => 'nullable|string|max:1000',
            'duration_months' => 'required|integer|min:1',
        ]);

        $booking = BookingRequest::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Gửi yêu cầu thành công, nhân viên sẽ sớm liên hệ',
            'data' => $booking
        ], 201);
    }
}
