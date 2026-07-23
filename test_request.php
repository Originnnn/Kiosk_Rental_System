<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/admin/contracts', 'POST', [
    'customer_name' => 'Nguyễn Văn Test',
    'customer_phone' => '0123456789',
    'kiosk_id' => 3, // K-12
    'start_date' => '2026-07-24',
    'end_date' => '2027-07-24',
    'payment_cycle' => '1',
    'deposit_amount' => 1000000,
    'total_amount' => 12000000,
    'actual_price_per_month' => 1000000,
]);
// Cần mock Auth login
$user = App\Models\User::first();
auth()->login($user);

$controller = $app->make(App\Http\Controllers\ContractController::class);
$response = $controller->store($request);

var_dump($response->isRedirection());
$kiosk = App\Models\Kiosk::find(3);
var_dump($kiosk->status);
