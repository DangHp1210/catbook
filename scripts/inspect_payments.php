<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();
Illuminate\Support\Facades\Facade::setFacadeApplication($app);

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "Columns:\n";
try {
    $cols = Schema::getColumnListing('payments');
    print_r($cols);
} catch (Throwable $e) {
    echo "Schema listing failed: " . $e->getMessage() . "\n";
}

echo "\nSample row:\n";
try {
    $rows = DB::table('payments')->limit(3)->get();
    foreach ($rows as $r) {
        print_r((array)$r);
    }
} catch (Throwable $e) {
    echo "Query failed: " . $e->getMessage() . "\n";
}
