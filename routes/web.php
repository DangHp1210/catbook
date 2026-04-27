<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
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

    Route::middleware('role:staff,admin')->prefix('admin')->name('admin.')->group(function () {
        Route::middleware('role:admin')->group(function () {
            Route::get('/', [AdminDashboardController::class, 'dashboard'])->name('panel');

            Route::get('/users', [AdminDashboardController::class, 'users'])->name('users.index');
            Route::patch('/users/{user}', [AdminDashboardController::class, 'updateUser'])->name('users.update');

            Route::get('/revenue', [AdminDashboardController::class, 'revenue'])->name('revenue.index');
        });

        Route::get('/books', [AdminDashboardController::class, 'books'])->name('books.index');
        Route::post('/books', [AdminDashboardController::class, 'storeBook'])->name('books.store');
        Route::patch('/books/{book}', [AdminDashboardController::class, 'updateBook'])->name('books.update');
        Route::delete('/books/{book}', [AdminDashboardController::class, 'destroyBook'])->name('books.destroy');

        Route::get('/categories', [AdminDashboardController::class, 'categories'])->name('categories.index');
        Route::post('/categories', [AdminDashboardController::class, 'storeCategory'])->name('categories.store');
        Route::patch('/categories/{category}', [AdminDashboardController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{category}', [AdminDashboardController::class, 'destroyCategory'])->name('categories.destroy');

        Route::get('/publishers', [AdminDashboardController::class, 'publishers'])->name('publishers.index');
        Route::post('/publishers', [AdminDashboardController::class, 'storePublisher'])->name('publishers.store');
        Route::patch('/publishers/{publisher}', [AdminDashboardController::class, 'updatePublisher'])->name('publishers.update');
        Route::delete('/publishers/{publisher}', [AdminDashboardController::class, 'destroyPublisher'])->name('publishers.destroy');

        Route::get('/authors', [AdminDashboardController::class, 'authors'])->name('authors.index');
        Route::post('/authors', [AdminDashboardController::class, 'storeAuthor'])->name('authors.store');
        Route::patch('/authors/{author}', [AdminDashboardController::class, 'updateAuthor'])->name('authors.update');
        Route::delete('/authors/{author}', [AdminDashboardController::class, 'destroyAuthor'])->name('authors.destroy');

        Route::get('/orders', [AdminDashboardController::class, 'orders'])->name('orders.index');
        Route::patch('/orders/{order}', [AdminDashboardController::class, 'updateOrder'])->name('orders.update');
    });

    Route::middleware('role:staff,admin')->group(function () {
        Route::get('/staff', function () {
            return view('staff.dashboard');
        })->name('staff.panel');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

