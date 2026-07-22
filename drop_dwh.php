<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

DB::connection('dwh')->statement('DROP TABLE IF EXISTS `fact.rental`');
DB::connection('dwh')->statement('DROP TABLE IF EXISTS `dim.kiosk`');
DB::connection('dwh')->statement('DROP TABLE IF EXISTS `dim.date`');
DB::connection('dwh')->statement('DROP TABLE IF EXISTS `Dim.Customer`');
DB::connection('dwh')->statement('DROP TABLE IF EXISTS `Dim.Kiosk`');
DB::connection('dwh')->statement('DROP TABLE IF EXISTS `Dim.Date`');
DB::connection('dwh')->statement('DROP TABLE IF EXISTS `Fact.Rental`');

echo "Dropped old DWH tables.\n";
