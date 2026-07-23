<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$contracts = App\Models\Contract::where('status', 'active')->get();
$fixed = 0;
foreach($contracts as $c) {
    if ($c->kiosk && $c->kiosk->status !== 'rented') {
        $c->kiosk->status = 'rented';
        $c->kiosk->save();
        $fixed++;
    }
}
echo "Fixed $fixed kiosks\n";
