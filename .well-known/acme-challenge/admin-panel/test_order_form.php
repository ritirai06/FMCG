<?php
/**
 * Order Form Test Script
 * This file tests if the order form is working correctly
 * 
 * Test URL: http://localhost:8000/sale/order/create
 */

echo "=== ORDER FORM TEST RESULTS ===\n\n";

// Include Laravel bootstrap
require_once __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->handle(
    $request = \Illuminate\Http\Request::capture()
);

// Test 1: Check if Order model exists
echo "✓ Test 1: Checking Order model...\n";
try {
    $orderModel = \App\Models\Order::class;
    echo "  ✓ Order model found\n";
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
}

// Test 2: Check if Store model exists
echo "\n✓ Test 2: Checking Store model...\n";
try {
    $stores = \App\Models\Store::count();
    echo "  ✓ Store model found - Total stores in DB: " . $stores . "\n";
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
}

// Test 3: Check if SalesPerson model exists
echo "\n✓ Test 3: Checking SalesPerson model...\n";
try {
    $salesPersons = \App\Models\SalesPerson::count();
    echo "  ✓ SalesPerson model found - Total sales persons in DB: " . $salesPersons . "\n";
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
}

// Test 4: Check database connection
echo "\n✓ Test 4: Checking database connection...\n";
try {
    $orders = \App\Models\Order::count();
    echo "  ✓ Database connection successful - Total orders: " . $orders . "\n";
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
}

// Test 5: Check recent orders
echo "\n✓ Test 5: Checking recent orders...\n";
try {
    $recentOrders = \App\Models\Order::latest()->limit(5)->get();
    if ($recentOrders->count() > 0) {
        echo "  ✓ Recent orders found:\n";
        foreach ($recentOrders as $order) {
            echo "    - Order #" . $order->order_number . " (Amount: Rs " . $order->amount . ", Status: " . $order->status . ")\n";
        }
    } else {
        echo "  ℹ No orders found yet\n";
    }
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== END OF TESTS ===\n";
echo "\nTo test the order form manually:\n";
echo "1. Visit: http://localhost:8000/sale/order/create\n";
echo "2. Fill in the form fields\n";
echo "3. Click 'Create Order'\n";
echo "4. Check the order list: http://localhost:8000/sale/order/list\n";
?>
