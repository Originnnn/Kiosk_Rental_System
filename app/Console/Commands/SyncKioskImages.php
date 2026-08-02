<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Models\Kiosk;
use App\Models\KioskImage;

class SyncKioskImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-kiosk-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync kiosk images from public/uploads/kiosks to database and normalize filenames';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $basePath = public_path('uploads/kiosks');
        
        if (!File::exists($basePath)) {
            $this->error("Directory does not exist: {$basePath}");
            return;
        }

        $directories = File::directories($basePath);

        foreach ($directories as $directory) {
            $folderName = basename($directory); // e.g., "k-9"
            $kioskCode = strtoupper($folderName); // e.g., "K-9"
            
            // Expected prefix e.g., "k9"
            $expectedPrefix = strtolower(str_replace('-', '', $folderName));
            
            $kiosk = Kiosk::where('code', $kioskCode)->first();
            
            if (!$kiosk) {
                $this->warn("Kiosk with code {$kioskCode} not found in database. Skipping folder {$folderName}.");
                continue;
            }

            $files = File::files($directory);
            
            foreach ($files as $file) {
                $fileName = $file->getFilename();
                
                // Format: k10_02_gocnghieng.png -> should be k9_02_gocnghieng.png
                $parts = explode('_', $fileName);
                $needsRename = false;
                
                if (count($parts) > 1 && strtolower($parts[0]) !== $expectedPrefix) {
                    $parts[0] = $expectedPrefix;
                    $needsRename = true;
                }

                $newFileName = implode('_', $parts);
                $newFilePath = $directory . DIRECTORY_SEPARATOR . $newFileName;
                
                if ($needsRename) {
                    File::move($file->getPathname(), $newFilePath);
                    $this->info("Renamed: {$fileName} -> {$newFileName}");
                }

                // Relative path for database: uploads/kiosks/k-9/k9_02_gocnghieng.png
                $relativePath = 'uploads/kiosks/' . $folderName . '/' . $newFileName;
                
                // Determine alt_text and sort_order based on filename
                $altText = 'Hình ảnh';
                $sortOrder = 5;
                
                $fileNameLower = strtolower($newFileName);
                
                if (str_contains($fileNameLower, 'mattien')) {
                    $altText = 'Mặt tiền';
                    $sortOrder = 1;
                } elseif (str_contains($fileNameLower, 'gocnghieng')) {
                    $altText = 'Góc nghiêng';
                    $sortOrder = 2;
                } elseif (str_contains($fileNameLower, 'cancanh')) {
                    $altText = 'Cận cảnh';
                    $sortOrder = 3;
                } elseif (str_contains($fileNameLower, 'matsau')) {
                    $altText = 'Mặt sau';
                    $sortOrder = 4;
                }

                // Update or Create the DB record
                KioskImage::updateOrCreate(
                    [
                        'kiosk_id' => $kiosk->id,
                        'file_path' => $relativePath,
                    ],
                    [
                        'alt_text' => $altText,
                        'sort_order' => $sortOrder,
                    ]
                );
                
                $this->info("Synced DB: {$relativePath}");
            }
        }
        
        $this->info('Kiosk images synced successfully!');
    }
}
