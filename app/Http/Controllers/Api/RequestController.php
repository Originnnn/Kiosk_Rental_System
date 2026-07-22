<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RentalRequest;
use App\Models\Customer;
use App\Models\RequestFile;
use Illuminate\Support\Str;

class RequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kiosk_id' => 'required|exists:kiosks,id',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',
            'desired_start' => 'required|date',
            'desired_end' => 'required|date|after_or_equal:desired_start',
            'note' => 'nullable|string',
            'files.*' => 'nullable|file|max:5120', // max 5MB
        ]);

        // Find or create customer
        $customer = Customer::firstOrCreate(
            ['email' => $validated['contact_email']],
            [
                'name' => $validated['contact_name'],
                'phone' => $validated['contact_phone']
            ]
        );

        $referenceCode = 'REQ-' . strtoupper(Str::random(8));

        $rentalRequest = RentalRequest::create([
            'reference_code' => $referenceCode,
            'kiosk_id' => $validated['kiosk_id'],
            'customer_id' => $customer->id,
            'contact_name' => $validated['contact_name'],
            'contact_email' => $validated['contact_email'],
            'contact_phone' => $validated['contact_phone'],
            'desired_start' => $validated['desired_start'],
            'desired_end' => $validated['desired_end'],
            'status' => 'new',
            'note' => $validated['note'] ?? null,
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('requests', 'public');
                RequestFile::create([
                    'rental_request_id' => $rentalRequest->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                ]);
            }
        }

        return $this->respondSuccess([
            'reference_code' => $referenceCode,
            'message' => 'Yêu cầu của bạn đã được gửi thành công.'
        ], 201);
    }

    public function showPublic($reference_code)
    {
        $req = RentalRequest::where('reference_code', $reference_code)->with('kiosk')->first();
        if (!$req) {
            return $this->respondError('Không tìm thấy yêu cầu', 404);
        }
        return $this->respondSuccess($req);
    }
}
