<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kiosk;
use App\Models\KioskImage;

class K10ImageSeeder extends Seeder
{
    public function run()
    {
        $kiosk = Kiosk::where('code', 'K-10')->first();
        if (!$kiosk) return;
        
        $files = glob(public_path('uploads/kiosks/k-10/*'));
        foreach ($files as $file) {
            $basename = basename($file);
            if ($basename == '.gitkeep') continue;
            
            $isMatTien = strpos($basename, 'mattien') !== false;
            KioskImage::updateOrCreate(
                ['kiosk_id' => $kiosk->id, 'file_path' => 'uploads/kiosks/k-10/' . $basename],
                ['alt_text' => 'Hình ảnh ' . $basename, 'sort_order' => $isMatTien ? 1 : 99]
            );
        }
    }
}
