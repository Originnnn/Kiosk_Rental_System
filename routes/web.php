<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\AdminRentalRequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\KioskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PortalController;

// --------------------------------------------------------------------------
// Nhóm 1: Cổng Khách hàng (kiosk.localhost)
// --------------------------------------------------------------------------
Route::domain(env('APP_URL_BASE', 'kiosk.localhost'))->group(function () {
    
    // Trang chủ / Sitemap
    Route::get('/', [PortalController::class, 'index'])->name('portal.index');
    
    // Xử lý form đăng ký thuê
    Route::post('/request', [PortalController::class, 'store'])->name('portal.store');



    // Xem chi tiết Kiosk
    Route::get('/kiosks/{id}', function ($id) {
        $kiosk = \App\Models\Kiosk::with(['position', 'images'])->findOrFail($id);
        return view('public.kiosks.show', compact('kiosk'));
    })->name('portal.kiosks.show');

    // Trang hiển thị form (nếu gọi get request)
    Route::get('/requests/create', function (Request $request) {
        $kioskId = $request->query('kiosk_id');
        $kiosk = null;
        if ($kioskId) {
            $kiosk = \App\Models\Kiosk::find($kioskId);
        }
        return view('public.requests.create', compact('kiosk'));
    });
});

// --------------------------------------------------------------------------
// Nhóm 2: Cổng Nhân viên (admin.kiosk.localhost)
// --------------------------------------------------------------------------
Route::domain('admin.' . env('APP_URL_BASE', 'kiosk.localhost'))->group(function () {
    
    // Authentication Routes
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', function (Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Theo yêu cầu: Login thành công thì redirect về / của domain admin
            return redirect('/');
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('email');
    });

    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');

    // Middleware Auth bọc toàn bộ các route quản lý
    Route::middleware('auth')->group(function () {
        
        // Route / của middleware auth trỏ về DashboardController@index
        Route::get('/', [DashboardController::class, 'index'])->name('admin.home');
        
        // Middleware cho Admin & Manager (Dashboard)
        Route::middleware('can:view-dashboard')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
            Route::get('/reports/export', [ReportController::class, 'export'])->name('admin.reports.export');
        });

        // Middleware cho Admin (Quản lý User)
        Route::middleware('can:is-admin')->group(function () {
            Route::resource('/users', UserController::class)->except(['create', 'show'])->names('admin.users');
            Route::patch('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('admin.users.toggleStatus');
        });

        // Middleware chung cho Manager & Employee (View Operations)
        Route::middleware('can:view-operations')->group(function () {
            Route::get('/rental-requests', [AdminRentalRequestController::class, 'index'])->name('admin.rental_requests.index');
            
            Route::get('/payments', [PaymentController::class, 'index'])->name('admin.payments.index');
            
            Route::get('/contracts', [ContractController::class, 'index'])->name('admin.contracts.index');
            Route::get('/contracts/{contract}', [ContractController::class, 'show'])->name('admin.contracts.show')->where('contract', '[0-9]+');
            
            Route::get('/customers', [CustomerController::class, 'index'])->name('admin.customers.index');
            Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('admin.customers.show')->where('id', '[0-9]+');
            
            Route::get('/kiosks', [KioskController::class, 'index'])->name('admin.kiosks.index');
            Route::get('/kiosks/{kiosk}', [KioskController::class, 'show'])->name('admin.kiosks.show')->where('kiosk', '[0-9]+');
        });

        // Middleware riêng cho Employee (Edit Operations)
        Route::middleware('can:edit-operations')->group(function () {
            Route::patch('/rental-requests/{id}/status', [AdminRentalRequestController::class, 'updateStatus'])->name('admin.rental_requests.updateStatus');
            
            Route::get('/payments/{id}/pay', [PaymentController::class, 'showPaymentForm'])->name('admin.payments.form');
            Route::put('/payments/{id}/process', [PaymentController::class, 'processPayment'])->name('admin.payments.process');

            Route::get('/contracts/create', [ContractController::class, 'create'])->name('admin.contracts.create');
            Route::post('/contracts', [ContractController::class, 'store'])->name('admin.contracts.store');
            Route::get('/contracts/{contract}/edit', [ContractController::class, 'edit'])->name('admin.contracts.edit');
            Route::put('/contracts/{contract}', [ContractController::class, 'update'])->name('admin.contracts.update');

            Route::post('/customers', [CustomerController::class, 'store'])->name('admin.customers.store');
            Route::get('/customers/{id}/edit', [CustomerController::class, 'edit'])->name('admin.customers.edit');
            Route::put('/customers/{id}', [CustomerController::class, 'update'])->name('admin.customers.update');
            Route::patch('/customers/{id}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('admin.customers.toggleStatus');

            Route::post('/kiosks', [KioskController::class, 'store'])->name('admin.kiosks.store');
            Route::put('/kiosks/{kiosk}', [KioskController::class, 'update'])->name('admin.kiosks.update');
        });
    });
});
