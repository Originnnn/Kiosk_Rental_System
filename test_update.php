<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$request = Illuminate\Http\Request::create('/kiosks/11', 'POST', [
    '_method' => 'PUT',
    'code' => 'K-11',
    'name' => 'Kiosk 11',
    'area' => '10',
    'price' => '1000',
]);
$controller = new App\Http\Controllers\KioskController();
try {
    $response = $controller->update($request, 11);
    echo $response->getContent();
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getFile() . ":" . $e->getLine();
}
