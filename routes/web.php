<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/danh-muc', [CatalogController::class, 'categories'])->name('catalog.categories');
Route::get('/danh-muc/{category:slug}', [CatalogController::class, 'category'])->name('catalog.category');
Route::get('/sach/{book:slug}', [CatalogController::class, 'book'])->name('catalog.book');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/gio-hang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/gio-hang/{book:slug}', [CartController::class, 'store'])->name('cart.store');
    Route::delete('/gio-hang/items/{item}', [CartController::class, 'destroy'])->name('cart.items.destroy');
    Route::get('/don-hang', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/don-hang/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/don-hang/{order}/huy', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/tai-khoan', function () {
        $user = request()->user()->loadCount(['addresses', 'orders', 'reviews', 'chatSessions']);

        return view('account.show', [
            'user' => $user,
        ]);
    })->name('account.show');

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

