<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$countBrands = DB::table('brands')->count();
$countCategories = DB::table('categories')->count();
$countProducts = DB::table('products')->count();

echo "brands: $countBrands\ncategories: $countCategories\nproducts: $countProducts\n";