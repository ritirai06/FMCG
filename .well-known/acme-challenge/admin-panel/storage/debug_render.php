<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo view('sale.order.create', [
        'stores' => collect(),
        'products' => collect(),
        'companySettings' => null,
        'companyName' => 'SalePanel',
        'user' => null,
        'salesPerson' => null,
    ])->render();
    echo "\nRENDER_OK\n";
} catch (Throwable $e) {
    echo get_class($e) . "\n";
    echo $e->getMessage() . "\n";
    echo $e->getFile() . ':' . $e->getLine() . "\n";
}
