<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreAndOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create stores
        $stores = [
            [
                'store_name' => 'Sharma General Store',
                'code' => 'SGS001',
                'manager' => 'Rajesh Sharma',
                'phone' => '9876543210',
                'address' => 'Andheri, Mumbai',
                'status' => true,
            ],
            [
                'store_name' => 'City Mart',
                'code' => 'CM002',
                'manager' => 'Priya Nair',
                'phone' => '9765432109',
                'address' => 'Ghatkopar, Mumbai',
                'status' => true,
            ],
            [
                'store_name' => 'Fresh Basket',
                'code' => 'FB003',
                'manager' => 'Amit Kumar',
                'phone' => '9654321098',
                'address' => 'Kanjurmarg, Mumbai',
                'status' => true,
            ],
            [
                'store_name' => 'Daily Needs Store',
                'code' => 'DNS004',
                'manager' => 'Sneha Patel',
                'phone' => '9543210987',
                'address' => 'Bhandup, Mumbai',
                'status' => true,
            ],
            [
                'store_name' => 'Quality Retail',
                'code' => 'QR005',
                'manager' => 'Vikram Singh',
                'phone' => '9432109876',
                'address' => 'Powai, Mumbai',
                'status' => true,
            ],
        ];

        // Insert stores
        \App\Models\Store::query()->delete();
        foreach ($stores as $store) {
            \App\Models\Store::create($store);
        }

        // Create orders for stores
        \App\Models\Order::query()->delete();
        
        $storeIds = \App\Models\Store::pluck('id')->toArray();
        $statuses = ['Pending', 'Processing', 'Out for Delivery', 'Delivered', 'Cancelled'];
        
        for ($i = 0; $i < 20; $i++) {
            \App\Models\Order::create([
                'order_number' => 'ORD' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'store_id' => $storeIds[array_rand($storeIds)],
                'customer' => 'Customer ' . ($i + 1),
                'order_date' => now()->subDays(rand(0, 28)),
                'amount' => rand(500, 50000),
                'status' => $statuses[array_rand($statuses)],
            ]);
        }

        echo "✓ Stores created: " . count($stores) . "\n";
        echo "✓ Orders created: 20\n";
    }
}
