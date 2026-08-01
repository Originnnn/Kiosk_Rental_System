<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Create a dummy image file
$dummyFilePath = __DIR__ . '/dummy.png';

$kiosk = App\Models\Kiosk::where('code', 'K-11')->first();
echo "Testing K-11 (ID " . $kiosk->id . ") with file upload\n";

$file = new \Illuminate\Http\UploadedFile(
    $dummyFilePath,
    'dummy.png',
    'image/png',
    null,
    true
);

$request = Illuminate\Http\Request::create('/kiosks/' . $kiosk->id, 'POST', [
    '_method' => 'PUT',
    'code' => 'K-11',
    'name' => 'Kiosk 11',
    'area' => '10',
    'price' => '1000',
], [], [
    'image_front' => $file
]);
$controller = new App\Http\Controllers\KioskController();
try {
    $response = $controller->update($request, $kiosk->id);
    echo "SUCCESS:\n";
    echo $response->getContent();
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getFile() . ":" . $e->getLine();
}
