<?php
// Boot Laravel and render the admin order preview partial for the first order.
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

// Boot the kernel to register providers
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

// Ensure facades work
Illuminate\Support\Facades\Facade::setFacadeApplication($app);

// Resolve the view factory
$viewFactory = $app['view'];

// Find first order
$orders = App\Models\Order::query()->with(['items.book', 'payments', 'user'])->limit(1)->get();

if ($orders->isEmpty()) {
    echo "No orders found in database.\n";
    exit(1);
}

$order = $orders->first();

try {
    $html = $viewFactory->make('admin.partials.order_detail', ['order' => $order])->render();
    echo $html;
} catch (Throwable $e) {
    echo "Render error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
    exit(2);
}
