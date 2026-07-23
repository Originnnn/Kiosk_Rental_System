<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

$u = User::where('email', 'employee@huebus.com')->first();
if (!$u) {
    echo "User not found\n";
    exit;
}

echo "Found user: {$u->email}\n";
echo "Password hash: {$u->password}\n";

$check1 = Hash::check('password', $u->password);
echo "Hash check for 'password': " . ($check1 ? 'OK' : 'FAIL') . "\n";

$auth = Auth::attempt(['email' => 'employee@huebus.com', 'password' => 'password']);
echo "Auth::attempt: " . ($auth ? 'OK' : 'FAIL') . "\n";
