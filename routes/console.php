<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

// Lên lịch chạy tiến trình bơm dữ liệu (ETL) vào lúc 00:00 mỗi ngày
Schedule::command('dwh:etl-sync')->dailyAt('00:00');

// Lên lịch kiểm tra thanh toán quá hạn lúc 01:00 mỗi ngày
Schedule::command('app:check-overdue-payments')->dailyAt('01:00');
