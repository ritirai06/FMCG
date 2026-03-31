<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Database Debug ===\n\n";

echo "1. Total Stores: " . \App\Models\Store::count() . "\n";
echo "2. Total Orders: " . \App\Models\Order::count() . "\n\n";

echo "3. Stores with Orders:\n";
$stores = \App\Models\Store::withCount(['orders'])
    ->with(['orders' => function($q) {
        $q->whereMonth('created_at', now()->month)
          ->select('store_id', 'status', 'amount', 'created_at');
    }])
    ->get();

foreach ($stores as $store) {
    echo "\nStore: {$store->store_name} ({$store->code})\n";
    echo "  - Manager: {$store->manager}\n";
    echo "  - Address: {$store->address}\n";
    echo "  - Total Orders Count: {$store->orders_count}\n";
    echo "  - This Month Orders: " . $store->orders->count() . "\n";
    
    $pending = $store->orders->whereIn('status', ['Pending', 'Processing', 'Out for Delivery'])->count();
    $delivered = $store->orders->where('status', 'Delivered')->count();
    $revenue = $store->orders->sum('amount');
    
    echo "  - Pending Tasks: {$pending}\n";
    echo "  - Delivered: {$delivered}\n";
    echo "  - Monthly Revenue: ₹{$revenue}\n";
}

echo "\n=== All Store Data ===\n";
print_r($stores->toArray());
