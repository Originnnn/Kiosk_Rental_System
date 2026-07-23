<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$req = Illuminate\Http\Request::create('http://admin.kiosk.localhost:8000/login', 'GET');
$app->instance('request', $req);
echo route('login');
