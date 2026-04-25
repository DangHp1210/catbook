<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin', function () {
            return view('role-panel', [
                'title' => 'Khu vực Admin',
                'description' => 'Quản trị toàn hệ thống: người dùng, sách, đơn hàng và cấu hình.',
                'theme' => 'rose',
            ]);
        })->name('admin.panel');
    });

    Route::middleware('role:staff,admin')->group(function () {
        Route::get('/staff', function () {
            return view('role-panel', [
                'title' => 'Khu vực Staff',
                'description' => 'Xử lý đơn hàng, chăm sóc khách hàng và kiểm duyệt nội dung vận hành.',
                'theme' => 'cyan',
            ]);
        })->name('staff.panel');
    });

    Route::middleware('role:customer')->group(function () {
        Route::get('/customer', function () {
            return view('role-panel', [
                'title' => 'Khu vực Customer',
                'description' => 'Quản lý thông tin cá nhân, lịch sử đơn hàng và trải nghiệm mua sách.',
                'theme' => 'amber',
            ]);
        })->name('customer.panel');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

