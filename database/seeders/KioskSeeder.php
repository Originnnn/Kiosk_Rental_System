<?php

namespace Database\Seeders;

use App\Models\Kiosk;
use App\Models\KioskPosition;
use Illuminate\Database\Seeder;

class KioskSeeder extends Seeder
{
    public function run(): void
    {
        // Mảng trạng thái giả định để random cho phong phú
        $statuses = ['available', 'reserved', 'rented'];

        // ==========================================
        // KHU A: Dãy Kiosk K-10 đến K-25
        // ==========================================
        $zoneA_startX = 370;
        $zoneA_deltaX = 63; // Khoảng cách giữa 2 kiosk (433 - 370 = 63)
        $zoneA_y = 49;
        $zoneA_width = 61;
        $zoneA_height = 76;
        $zoneA_name = 'Khu A';

        for ($i = 10; $i <= 25; $i++) {
            $currentX = $zoneA_startX + (($i - 10) * $zoneA_deltaX);
            
            $fakePrice = rand(40, 80) * 100000; 
            $fakeArea = rand(150, 250) / 10;
            $randomStatus = $statuses[array_rand($statuses)];

            $kiosk = Kiosk::create([
                'code' => 'K-' . $i,
                'name' => 'K-' . $i,
                'description' => 'Ki ốt khu vực mặt tiền số ' . $i,
                'area' => $fakeArea,
                'price' => $fakePrice,
                'status' => $randomStatus,
            ]);
            
            KioskPosition::create([
                'kiosk_id' => $kiosk->id,
                'x' => $currentX,
                'y' => $zoneA_y,
                'width' => $zoneA_width,
                'height' => $zoneA_height,
                'zone' => $zoneA_name,
            ]);
        }

        // ==========================================
        // KHU B: Dãy Kiosk K-26 đến K-34
        // ==========================================
        $zoneB_startX = 1635;
        $zoneB_startY = 49;
        $zoneB_deltaY = 40; // 89 - 49 = 40
        $zoneB_width = 126;
        $zoneB_height = 38;
        $zoneB_name = 'Khu B';

        for ($i = 26; $i <= 34; $i++) {
            $currentY = $zoneB_startY + (($i - 26) * $zoneB_deltaY);

            $fakePrice = rand(40, 80) * 100000; 
            $fakeArea = rand(150, 250) / 10;
            $randomStatus = $statuses[array_rand($statuses)];

            $kiosk = Kiosk::create([
                'code' => 'K-' . $i,
                'name' => 'K-' . $i,
                'description' => 'Ki ốt dãy 26-34',
                'area' => $fakeArea,
                'price' => $fakePrice,
                'status' => $randomStatus,
            ]);
            
            KioskPosition::create([
                'kiosk_id' => $kiosk->id,
                'x' => $zoneB_startX,
                'y' => $currentY,
                'width' => $zoneB_width,
                'height' => $zoneB_height,
                'zone' => $zoneB_name,
            ]);
        }
    }
}