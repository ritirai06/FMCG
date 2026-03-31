<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Check orders with stores
$orders = \App\Models\Order::with('store')->latest()->take(3)->get();
echo "=== RECENT ORDERS ===\n";
echo "Orders found: " . $orders->count() . "\n";
foreach($orders as $o) {
    echo "- Order #{$o->id} | Store: {$o->store?->store_name} | Status: {$o->status} | Amount: {$o->amount}\n";
}

// Check stores
$stores = \App\Models\Store::limit(3)->get();
echo "\n=== ASSIGNED STORES ===\n";
echo "Stores found: " . $stores->count() . "\n";
foreach($stores as $s) {
    echo "- Store: {$s->store_name} | Address: {$s->address} | Manager: {$s->manager}\n";
}

echo "\n=== VERIFICATION COMPLETE ===\n";
