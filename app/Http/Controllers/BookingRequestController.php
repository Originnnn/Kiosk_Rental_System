<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use Illuminate\Http\Request;

use App\Models\User;
use App\Notifications\NewRentRequestNotification;
use Illuminate\Support\Facades\Notification;

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

        // Notify Admins, Managers and Employees
        $admins = User::whereIn('role', ['admin', 'manager', 'employee'])->get();
        Notification::send($admins, new NewRentRequestNotification($booking));

        return response()->json([
            'success' => true,
            'message' => 'Yêu cầu thuê của bạn đã được gửi thành công. Chúng tôi sẽ liên hệ lại sớm nhất!',
            'data' => $booking
        ], 201);
    }
}
