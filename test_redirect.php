<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$req = Illuminate\Http\Request::create('http://admin.kiosk.localhost:8000/login', 'POST');
$app->instance('request', $req);

echo "url('/'): " . url('/') . "\n";
echo "redirect('/'): " . redirect('/')->getTargetUrl() . "\n";
