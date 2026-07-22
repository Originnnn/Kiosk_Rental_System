<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$fact = DB::connection('dwh')->select('DESCRIBE `fact.rental`');
$dim = DB::connection('dwh')->select('DESCRIBE `dim.date`');

echo "fact.rental schema:\n";
print_r($fact);

echo "\ndim.date schema:\n";
print_r($dim);
