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
use App\Http\Controllers\ProfileController;

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
        $allKiosks = \App\Models\Kiosk::with('position')->get();
        return view('public.kiosks.show', compact('kiosk', 'allKiosks'));
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
            
            if (Auth::user()->role === 'employee') {
                return redirect()->route('admin.alerts.index');
            }
            return redirect()->route('admin.dashboard');
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
        
        // Route / của middleware auth trỏ về trang chủ tương ứng với role
        Route::get('/', function () {
            if (Auth::user()->role === 'employee') {
                return redirect()->route('admin.alerts.index');
            }
            return redirect()->route('admin.dashboard');
        })->name('admin.home');
        
        // Hồ sơ cá nhân
        Route::get('/profile', [ProfileController::class, 'index'])->name('admin.profile.index');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('admin.profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
        Route::get('/profile/password', [ProfileController::class, 'password'])->name('admin.profile.password');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('admin.profile.password.update');

        // Notifications
        Route::get('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'read'])->name('admin.notifications.read');

        // Middleware cho Admin & Manager (Dashboard)
        Route::middleware('can:view-dashboard')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
            Route::get('/reports/export', [ReportController::class, 'export'])->name('admin.reports.export');
        });

        // Nhóm Quản lý User
        Route::resource('/users', UserController::class)->except(['create', 'show'])->names('admin.users');
        Route::patch('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('admin.users.toggleStatus');

        // Nhóm Dữ liệu Vận hành (Operations)
        Route::get('/rental-requests', [AdminRentalRequestController::class, 'index'])->name('admin.rental_requests.index');
        Route::patch('/rental-requests/{id}/status', [AdminRentalRequestController::class, 'updateStatus'])->name('admin.rental_requests.updateStatus');
        
        Route::get('/payments', [PaymentController::class, 'index'])->name('admin.payments.index');
        Route::get('/payments/{id}/pay', [PaymentController::class, 'showPaymentForm'])->name('admin.payments.form');
        Route::put('/payments/{id}/process', [PaymentController::class, 'processPayment'])->name('admin.payments.process');

        Route::get('/contracts', [ContractController::class, 'index'])->name('admin.contracts.index');
        Route::get('/contracts/create', [ContractController::class, 'create'])->name('admin.contracts.create');
        Route::post('/contracts', [ContractController::class, 'store'])->name('admin.contracts.store');
        Route::get('/contracts/{contract}', [ContractController::class, 'show'])->name('admin.contracts.show')->where('contract', '[0-9]+');
        Route::get('/contracts/{contract}/edit', [ContractController::class, 'edit'])->name('admin.contracts.edit');
        Route::put('/contracts/{contract}', [ContractController::class, 'update'])->name('admin.contracts.update');

        Route::get('/customers', [CustomerController::class, 'index'])->name('admin.customers.index');
        Route::post('/customers', [CustomerController::class, 'store'])->name('admin.customers.store');
        Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('admin.customers.show')->where('id', '[0-9]+');
        Route::get('/customers/{id}/edit', [CustomerController::class, 'edit'])->name('admin.customers.edit');
        Route::put('/customers/{id}', [CustomerController::class, 'update'])->name('admin.customers.update');
        Route::patch('/customers/{id}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('admin.customers.toggleStatus');

        Route::get('/kiosks', [KioskController::class, 'index'])->name('admin.kiosks.index');
        Route::post('/kiosks', [KioskController::class, 'store'])->name('admin.kiosks.store');
        Route::get('/kiosks/{kiosk}', [KioskController::class, 'show'])->name('admin.kiosks.show')->where('kiosk', '[0-9]+');
        Route::put('/kiosks/{kiosk}', [KioskController::class, 'update'])->name('admin.kiosks.update');

        // Routes riêng cho Employee (Alerts & Reports)
        Route::middleware(\App\Http\Middleware\CheckEmployeeRole::class)->group(function () {
            Route::get('/alerts', [\App\Http\Controllers\AlertController::class, 'index'])->name('admin.alerts.index');
            Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('admin.reports.index');
        });
    });
});
