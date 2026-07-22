<?php

namespace Database\Seeders;

use App\Models\Kiosk;
use App\Models\KioskPosition;
use Illuminate\Database\Seeder;

class KioskSeeder extends Seeder
{
    public function run(): void
    {
        // Khai báo các thông số cố định của dãy K-10 đến K-25
        $startX = 370;
        $deltaX = 63; // Khoảng cách giữa 2 kiosk (433 - 370 = 63)
        $y = 49;
        $width = 61;
        $height = 76;
        $zone = 'Khu A';

        // Mảng trạng thái giả định để random cho phong phú
        $statuses = ['available', 'reserved', 'rented'];

        for ($i = 10; $i <= 25; $i++) {
            // Tự động tính tọa độ X mới: K-10 = 370, K-11 = 433, K-12 = 496...
            $currentX = $startX + (($i - 10) * $deltaX);

            // Fake giá từ 4,000,000 đến 8,000,000 và diện tích từ 15 đến 25
            $fakePrice = rand(40, 80) * 100000; 
            $fakeArea = rand(150, 250) / 10;
            
            // Random trạng thái
            $randomStatus = $statuses[array_rand($statuses)];

            // 1. Tạo dữ liệu Kiosk
            $kiosk = Kiosk::create([
                'code' => 'K-' . $i,
                'name' => 'Ki ốt ' . $i,
                'description' => 'Ki ốt khu vực mặt tiền số ' . $i,
                'area' => $fakeArea,
                'price' => $fakePrice,
                'status' => $randomStatus,
            ]);
            
            // 2. Tạo tọa độ (Position) tương ứng
            KioskPosition::create([
                'kiosk_id' => $kiosk->id,
                'x' => $currentX,
                'y' => $y,
                'width' => $width,
                'height' => $height,
                'zone' => $zone,
            ]);
        }
    }
}