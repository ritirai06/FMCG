<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use App\Models\AdminSetting;
use App\Models\Category;
use App\Models\Brand;
use App\Models\City;
use App\Models\Locality;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password')
            ]
        );

        Admin::firstOrCreate(
            ['email' => 'admin@fmcg.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin1234'),
                'role' => 'admin',
            ]
        );

        // Create default admin setting
        AdminSetting::firstOrCreate(
            ['id' => 1],
            [
                'company_name' => 'Admin Panel',
                'profile_image' => 'default.png',
            ]
        );

        // Ensure at least one Category exists so dropdowns have values
        Category::firstOrCreate(
            ['slug' => 'default-category'],
            [
                'name' => 'Default Category',
                'description' => 'Auto-created default category',
                'status' => 'Active'
            ]
        );

        // Create default brands so product creation has options
        $defaultBrands = [
            ['name' => 'Brand A', 'slug' => 'brand-a'],
            ['name' => 'Brand B', 'slug' => 'brand-b'],
            ['name' => 'Brand C', 'slug' => 'brand-c'],
        ];

        foreach ($defaultBrands as $brand) {
            Brand::firstOrCreate(
                ['slug' => $brand['slug']],
                ['name' => $brand['name']]
            );
        }

        // Create default cities and localities for warehouse management
        $cities = [
            ['name' => 'Delhi', 'state' => 'Delhi'],
            ['name' => 'Mumbai', 'state' => 'Maharashtra'],
            ['name' => 'Bangalore', 'state' => 'Karnataka'],
            ['name' => 'Hyderabad', 'state' => 'Telangana'],
        ];

        foreach ($cities as $cityData) {
            $city = City::firstOrCreate(
                ['name' => $cityData['name'], 'state' => $cityData['state']],
                ['status' => 'Active']
            );

            // Add default localities to each city
            $localities = [
                ['name' => 'Central', 'pincode' => '100001'],
                ['name' => 'North', 'pincode' => '100002'],
                ['name' => 'South', 'pincode' => '100003'],
                ['name' => 'East', 'pincode' => '100004'],
                ['name' => 'West', 'pincode' => '100005'],
            ];

            foreach ($localities as $locData) {
                Locality::firstOrCreate(
                    [
                        'city_id' => $city->id,
                        'name' => $cityData['name'] . ' - ' . $locData['name']
                    ],
                    [
                        'pincode' => $locData['pincode'],
                        'status' => 'Active'
                    ]
                );
            }
        }

        // seed subcategories after categories exist
        $this->call(\Database\Seeders\SubCategorySeeder::class);

        // Create default products for inventory
        $products = [
            ['name' => 'Rice (1kg)', 'sku' => 'RICE001', 'category' => 'Default Category', 'brand' => 'Brand A', 'purchase_price' => 40, 'sale_price' => 50, 'mrp' => 60, 'margin' => 10],
            ['name' => 'Wheat (1kg)', 'sku' => 'WHEAT001', 'category' => 'Default Category', 'brand' => 'Brand A', 'purchase_price' => 35, 'sale_price' => 45, 'mrp' => 55, 'margin' => 10],
            ['name' => 'Sugar (1kg)', 'sku' => 'SUGAR001', 'category' => 'Default Category', 'brand' => 'Brand B', 'purchase_price' => 45, 'sale_price' => 55, 'mrp' => 65, 'margin' => 10],
            ['name' => 'Oil (1L)', 'sku' => 'OIL001', 'category' => 'Default Category', 'brand' => 'Brand B', 'purchase_price' => 100, 'sale_price' => 120, 'mrp' => 150, 'margin' => 20],
            ['name' => 'Salt (1kg)', 'sku' => 'SALT001', 'category' => 'Default Category', 'brand' => 'Brand C', 'purchase_price' => 20, 'sale_price' => 25, 'mrp' => 30, 'margin' => 5],
        ];

        foreach ($products as $product) {
            \App\Models\Product::firstOrCreate(
                ['sku' => $product['sku']],
                [
                    'name' => $product['name'],
                    'category' => $product['category'],
                    'brand' => $product['brand'],
                    'purchase_price' => $product['purchase_price'],
                    'sale_price' => $product['sale_price'],
                    'mrp' => $product['mrp'],
                    'margin' => $product['margin'],
                    'status' => 'Active'
                ]
            );
        }
    }
}
