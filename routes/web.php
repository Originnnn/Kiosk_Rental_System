<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Kiosk;

Route::get('/', function () {
    return view('public.sitemap');
});

Route::get('/kiosks', function (Request $request) {
    $query = Kiosk::with(['position', 'images']);
    
    if ($request->filled('q')) {
        $query->where(function($q) use ($request) {
            $q->where('code', 'like', '%' . $request->q . '%')
              ->orWhere('name', 'like', '%' . $request->q . '%');
        });
    }

    if ($request->filled('zone')) {
        $query->whereHas('position', function($q) use ($request) {
            $q->where('zone', $request->zone);
        });
    }
    
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    
    $kiosks = $query->get();
    
    return view('public.kiosks.index', compact('kiosks'));
});

Route::get('/kiosks/{id}', function ($id) {
    $kiosk = Kiosk::with(['position', 'images'])->findOrFail($id);
    return view('public.kiosks.show', compact('kiosk'));
});

Route::get('/requests/create', function (Request $request) {
    $kioskId = $request->query('kiosk_id');
    $kiosk = null;
    if ($kioskId) {
        $kiosk = Kiosk::find($kioskId);
    }
    return view('public.requests.create', compact('kiosk'));
});

use App\Http\Controllers\AdminRentalRequestController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\PaymentController;

Route::prefix('admin')->middleware('auth')->group(function () {
    // Middleware cho Admin & Manager (Dashboard)
    Route::middleware('can:view-dashboard')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/reports/export', [\App\Http\Controllers\ReportController::class, 'export'])->name('admin.reports.export');
    });

    // Middleware cho Admin (Quản lý User)
    Route::middleware('can:is-admin')->group(function () {
        Route::resource('/users', \App\Http\Controllers\UserController::class)->except(['create', 'edit', 'show'])->names('admin.users');
        Route::patch('/users/{id}/toggle-status', [\App\Http\Controllers\UserController::class, 'toggleStatus'])->name('admin.users.toggleStatus');
    });

    // Middleware chung cho Manager & Employee (View Operations)
    Route::middleware('can:view-operations')->group(function () {
        Route::get('/rental-requests', [AdminRentalRequestController::class, 'index']);
        
        Route::get('/payments', [PaymentController::class, 'index'])->name('admin.payments.index');
        
        Route::get('/contracts', [\App\Http\Controllers\ContractController::class, 'index'])->name('admin.contracts.index');
        Route::get('/contracts/{contract}', [\App\Http\Controllers\ContractController::class, 'show'])->name('admin.contracts.show')->where('contract', '[0-9]+');
        
        Route::get('/customers', [\App\Http\Controllers\CustomerController::class, 'index'])->name('admin.customers.index');
        Route::get('/customers/{id}', [\App\Http\Controllers\CustomerController::class, 'show'])->name('admin.customers.show')->where('id', '[0-9]+');
        
        Route::get('/kiosks', [\App\Http\Controllers\KioskController::class, 'index'])->name('admin.kiosks.index');
        Route::get('/kiosks/{kiosk}', [\App\Http\Controllers\KioskController::class, 'show'])->name('admin.kiosks.show')->where('kiosk', '[0-9]+');
    });

    // Middleware riêng cho Employee (Edit Operations)
    Route::middleware('can:edit-operations')->group(function () {
        Route::patch('/rental-requests/{id}/status', [AdminRentalRequestController::class, 'updateStatus']);
        
        Route::get('/payments/{id}/pay', [PaymentController::class, 'showPaymentForm'])->name('admin.payments.form');
        Route::put('/payments/{id}/process', [PaymentController::class, 'processPayment'])->name('admin.payments.process');

        Route::get('/contracts/create', [\App\Http\Controllers\ContractController::class, 'create'])->name('admin.contracts.create');
        Route::post('/contracts', [\App\Http\Controllers\ContractController::class, 'store'])->name('admin.contracts.store');
        Route::get('/contracts/{contract}/edit', [\App\Http\Controllers\ContractController::class, 'edit'])->name('admin.contracts.edit');
        Route::put('/contracts/{contract}', [\App\Http\Controllers\ContractController::class, 'update'])->name('admin.contracts.update');

        Route::post('/customers', [\App\Http\Controllers\CustomerController::class, 'store'])->name('admin.customers.store');
        Route::get('/customers/{id}/edit', [\App\Http\Controllers\CustomerController::class, 'edit'])->name('admin.customers.edit');
        Route::put('/customers/{id}', [\App\Http\Controllers\CustomerController::class, 'update'])->name('admin.customers.update');
        Route::patch('/customers/{id}/toggle-status', [\App\Http\Controllers\CustomerController::class, 'toggleStatus'])->name('admin.customers.toggleStatus');

        Route::post('/kiosks', [\App\Http\Controllers\KioskController::class, 'store'])->name('admin.kiosks.store');
        Route::put('/kiosks/{kiosk}', [\App\Http\Controllers\KioskController::class, 'update'])->name('admin.kiosks.update');
    });
});

// Authentication Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (\Illuminate\Support\Facades\Auth::attempt($credentials)) {
        $request->session()->regenerate();
        
        if (auth()->user()->role === 'employee') {
            return redirect('/admin/contracts');
        }
        return redirect('/admin/dashboard');
    }

    return back()->withErrors([
        'email' => 'Thông tin đăng nhập không chính xác.',
    ])->onlyInput('email');
});

Route::post('/logout', function (Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');
