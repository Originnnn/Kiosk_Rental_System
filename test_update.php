<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$k = App\Models\Kiosk::first();
var_dump($k->status);
var_dump($k->update(['status' => 'rented']));
var_dump($k->refresh()->status);
