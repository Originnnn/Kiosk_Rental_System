<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SitemapController;
use App\Http\Controllers\Api\KioskController;
use App\Http\Controllers\Api\RequestController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\BookingRequestController;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
| Các endpoint mở cho khách hàng xem sơ đồ, tìm kiosk và gửi yêu cầu thuê.
*/
Route::get('/sitemap', [SitemapController::class, 'index']);
Route::get('/kiosks', [KioskController::class, 'index']);
Route::get('/kiosks/{id}', [KioskController::class, 'show']);
Route::post('/requests', [RequestController::class, 'store']);
Route::get('/requests/public/{reference_code}', [RequestController::class, 'showPublic']);
Route::post('/rental-requests', [BookingRequestController::class, 'store']);

/*
|--------------------------------------------------------------------------
| Internal API Routes
|--------------------------------------------------------------------------
| Yêu cầu Authorization: Bearer <token> (sử dụng Sanctum middleware)
*/
Route::middleware('auth:sanctum')->group(function () {
    
    // Request Management
    Route::get('/requests', [RequestController::class, 'index']);
    Route::get('/requests/{id}', [RequestController::class, 'show']);
    Route::patch('/requests/{id}', [RequestController::class, 'update']);
    
    // Contract Management
    Route::post('/contracts', [ContractController::class, 'store']);
    Route::get('/contracts/{id}', [ContractController::class, 'show']);
    
    // Payments
    Route::post('/payments', [PaymentController::class, 'store']);
    
    // Reports (Dành cho lãnh đạo)
    Route::get('/reports/revenue', [ReportController::class, 'revenue']);
    Route::get('/reports/occupancy', [ReportController::class, 'occupancy']);
    
    // User & Audit Logs Management (Dành cho admin)
    Route::apiResource('users', UserController::class);
    Route::get('/audit-logs', [AuditLogController::class, 'index']);
});