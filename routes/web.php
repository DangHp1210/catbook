<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PaymentController;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Order;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/danh-muc', [CatalogController::class, 'categories'])->name('catalog.categories');
Route::get('/danh-muc/{category:slug}', [CatalogController::class, 'category'])->name('catalog.category');
Route::get('/sach/{book:slug}', [CatalogController::class, 'book'])->name('catalog.book');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/login/{provider}', [AuthController::class, 'redirectToProvider'])->name('login.provider');
    Route::get('/login/{provider}/callback', [AuthController::class, 'handleProviderCallback'])->name('login.provider.callback');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/gio-hang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/gio-hang/{book:slug}', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/gio-hang/items/{item}', [CartController::class, 'update'])->name('cart.items.update');
    Route::delete('/gio-hang/items/{item}', [CartController::class, 'destroy'])->name('cart.items.destroy');
    Route::get('/don-hang', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/don-hang/{order:order_code}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/don-hang/{order:order_code}/huy', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/don-hang/{order:order_code}/danh-gia/{book:slug}', [ReviewController::class, 'store'])->name('orders.reviews.store');
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Payment routes
    Route::get('/payment/vnpay', [PaymentController::class, 'createPayment'])->name('payment.vnpay');
    Route::get('/payment/momo', [PaymentController::class, 'createMomoPayment'])->name('payment.momo');

    Route::get('/tai-khoan', function () {
        $user = request()->user();
        $user->loadCount(['addresses', 'reviews', 'chatSessions']);
        
        // Count all displayable orders (active + completed + cancelled)
        $user->orders_count = $user->orders()
            ->whereIn('order_status', ['pending', 'confirmed', 'shipping', 'completed', 'cancelled', 'refunded'])
            ->count();

        return view('account.show', [
            'user' => $user,
        ]);
    })->name('account.show');

    Route::post('/tai-khoan/avatar', [AccountController::class, 'updateAvatar'])->name('account.avatar.update');
    Route::patch('/tai-khoan/thong-tin', [AccountController::class, 'updateProfile'])->name('account.profile.update');
    Route::patch('/tai-khoan/doi-mat-khau', [AccountController::class, 'changePassword'])->name('account.password.update');

    // Address management
    Route::get('/tai-khoan/dia-chi', [\App\Http\Controllers\UserAddressController::class, 'index'])->name('account.addresses.index');
    Route::post('/tai-khoan/dia-chi', [\App\Http\Controllers\UserAddressController::class, 'store'])->name('account.addresses.store');
    Route::patch('/tai-khoan/dia-chi/{address}', [\App\Http\Controllers\UserAddressController::class, 'update'])->name('account.addresses.update');
    Route::delete('/tai-khoan/dia-chi/{address}', [\App\Http\Controllers\UserAddressController::class, 'destroy'])->name('account.addresses.destroy');
    Route::post('/tai-khoan/dia-chi/{address}/mac-dinh', [\App\Http\Controllers\UserAddressController::class, 'setDefault'])->name('account.addresses.set_default');

    // Admin routes - prefix /admin with role:admin middleware
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // Admin dashboard
        Route::get('/', [AdminDashboardController::class, 'dashboard'])->name('panel');

        // Admin-only resources
        Route::get('/users', [AdminDashboardController::class, 'users'])->name('users.index');
        Route::patch('/users/{user}', [AdminDashboardController::class, 'updateUser'])->name('users.update');

        Route::get('/revenue', [AdminDashboardController::class, 'revenue'])->name('revenue.index');

        // Shared resources (accessible to admin)
        Route::get('/books', [AdminDashboardController::class, 'books'])->name('books.index');
        Route::get('/books/authors/search', [AdminDashboardController::class, 'searchAuthors'])->name('books.authors.search');
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
        Route::get('/orders/{order}/preview', [AdminDashboardController::class, 'previewOrder'])->name('orders.preview');
        Route::patch('/orders/{order}', [AdminDashboardController::class, 'updateOrder'])->name('orders.update');
    });

    // Staff routes - prefix /staff with role:staff middleware
    Route::middleware('role:staff')->prefix('staff')->name('staff.')->group(function () {
        // Staff dashboard
        Route::get('/', function () {
            $stats = [
                'books' => Book::query()->count(),
                'authors' => Author::query()->count(),
                'categories' => Category::query()->count(),
                'publishers' => Publisher::query()->count(),
                'orders' => Order::query()->count(),
            ];

            return view('staff.dashboard', [
                'stats' => $stats,
            ]);
        })->name('panel');

        // Shared resources (accessible to staff)
        Route::get('/books', [AdminDashboardController::class, 'books'])->name('books.index');
        Route::get('/books/authors/search', [AdminDashboardController::class, 'searchAuthors'])->name('books.authors.search');
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

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Route nhận kết quả trả về từ VNPay
Route::get('/vnpay-return', [PaymentController::class, 'vnpayReturn'])->name('vnpay.return');

// Route nhận kết quả trả về từ MoMo
Route::get('/momo-return', [PaymentController::class, 'momoReturn'])->name('momo.return');

// Webhook từ MoMo (IPN) - không cần auth
Route::post('/momo-ipn', [PaymentController::class, 'momoWebhook'])->name('momo.webhook');
