<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateSampleOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:create-sample';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create sample orders for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if orders already exist
        $existingOrders = Order::count();
        if ($existingOrders > 0) {
            $this->info("✅ Orders already exist (" . $existingOrders . " total)");
            return;
        }

        // Get or create test data
        $store = Store::first();
        if (!$store) {
            $this->error("❌ No store found! Create a store first.");
            return;
        }

        $user = User::where('role', 'admin')->first();
        if (!$user) {
            $this->error("❌ No admin user found! Please create an admin user first.");
            return;
        }

        // Insert 10 sample orders
        $statuses = ['Pending', 'Delivered', 'Cancelled'];
        $customers = ['Ravi Kumar', 'Neha Verma', 'Amit Singh', 'Priya Sharma', 'John Doe', 'Sarah Wilson', 'Rajesh Patel', 'Ananya Gupta', 'Vikram Khan', 'Pooja Reddy'];
        
        $bar = $this->output->createProgressBar(10);
        $bar->start();

        for ($i = 1; $i <= 10; $i++) {
            Order::create([
                'order_number' => 'ORD' . date('Y') . sprintf('%04d', $i),
                'store_id' => $store->id,
                'customer_name' => $customers[$i - 1],
                'customer_phone' => '987654' . sprintf('%04d', $i),
                'total_amount' => rand(500, 5000),
                'status' => $statuses[array_rand($statuses)],
                'created_by' => $user->id,
                'order_date' => Carbon::now()->subDays(rand(0, 30)),
                'created_at' => Carbon::now()->subDays(rand(0, 30)),
                'updated_at' => Carbon::now()->subDays(rand(0, 30)),
            ]);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("✅ Successfully created 10 sample orders!");
        $this->info("📊 Total orders: " . Order::count());
        $this->line("");
        $this->line("Visit: http://127.0.0.1:8000/orders");
    }
}
