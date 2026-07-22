<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookingRequest;
use App\Models\Kiosk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminRentalRequestController extends Controller
{
    public function index()
    {
        // Sử dụng BookingRequest đã tạo ở bước trước (thay vì rental_requests cũ)
        $requests = BookingRequest::with('kiosk')->latest()->get();
        return view('admin.rental_requests.index', compact('requests'));
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,resolved,rejected'
        ]);

        try {
            DB::beginTransaction();

            $bookingRequest = BookingRequest::findOrFail($id);
            $bookingRequest->status = $validated['status'];
            
            // Ghi lại ID nhân viên xử lý, nếu chưa có hệ thống Auth hoàn chỉnh thì giả lập là 1
            $bookingRequest->handled_by = Auth::id() ?? 1; 

            // Nếu trạng thái được duyệt (resolved), chuyển đổi trạng thái Kiosk thành rented
            if ($validated['status'] === 'resolved') {
                $kiosk = Kiosk::findOrFail($bookingRequest->kiosk_id);
                $kiosk->status = 'rented';
                $kiosk->save();
            }

            $bookingRequest->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công!',
                'data' => $bookingRequest
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ], 500);
        }
    }
}
