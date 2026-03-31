<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Add product_id foreign key if not exists
            if (!Schema::hasColumn('order_items', 'product_id')) {
                $table->foreignId('product_id')->nullable()->after('order_id')->constrained('products')->nullOnDelete();
            }

            // Change price column name to unit_price if needed
            if (Schema::hasColumn('order_items', 'price') && !Schema::hasColumn('order_items', 'unit_price')) {
                $table->renameColumn('price', 'unit_price');
            }

            // Ensure subtotal column exists
            if (!Schema::hasColumn('order_items', 'subtotal')) {
                $table->decimal('subtotal', 12, 2)->default(0)->after('unit_price')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'product_id')) {
                $table->dropConstrainedForeignId('product_id');
            }
        });
    }
};
