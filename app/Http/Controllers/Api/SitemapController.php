<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kiosk;

class SitemapController extends Controller
{
    public function index()
    {
        // Lấy danh sách kiosk kèm theo quan hệ 'position'
        $kiosks = Kiosk::with('position')->get()->map(function ($kiosk) {
            return [
                'kioskId' => $kiosk->id,
                'code' => $kiosk->code,
                'status' => $kiosk->status,
                'price' => $kiosk->price,
                // Lấy tọa độ từ bảng kiosk_positions, nếu chưa có thì gán mặc định
                'x' => $kiosk->position->x ?? 0,
                'y' => $kiosk->position->y ?? 0,
                'width' => $kiosk->position->width ?? 100,
                'height' => $kiosk->position->height ?? 100,
                'zone' => $kiosk->position->zone ?? 'Chưa rõ',
            ];
        });

        // Trả về response chuẩn hóa đã viết ở file Controller gốc
        return $this->respondSuccess([
            'backgroundImage' => asset('maps/sitemap.jpg'),
            'zones' => [
                ['id' => 'A', 'name' => 'Khu A']
            ],
            'kiosks' => $kiosks
        ]);
    }
}