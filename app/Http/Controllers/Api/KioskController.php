<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kiosk;

class KioskController extends Controller
{
    public function index(Request $request)
    {
        $query = Kiosk::with('position', 'images');

        if ($request->filled('zone')) {
            $query->whereHas('position', function($q) use ($request) {
                $q->where('zone', $request->zone);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $kiosks = $query->get();

        return $this->respondSuccess($kiosks);
    }

    public function show($id)
    {
        $kiosk = Kiosk::with(['position', 'images'])->find($id);

        if (!$kiosk) {
            return $this->respondError('Kiosk not found', 404);
        }

        return $this->respondSuccess($kiosk);
    }
}
