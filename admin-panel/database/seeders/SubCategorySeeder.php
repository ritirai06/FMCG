<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // insert some dummy subcategories associated with existing category ids
        // make sure you have at least one category in `categories` table, e.g. id 1
        $now = now();
        DB::table('subcategories')->insertOrIgnore([
            [
                'name' => 'Sample Subcategory A',
                'slug' => Str::slug('Sample Subcategory A'),
                'description' => 'Dummy data for testing',
                'image' => null,
                'category_id' => 1,
                'status' => 'Active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Sample Subcategory B',
                'slug' => Str::slug('Sample Subcategory B'),
                'description' => 'Another dummy entry',
                'image' => null,
                'category_id' => 1,
                'status' => 'Active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
