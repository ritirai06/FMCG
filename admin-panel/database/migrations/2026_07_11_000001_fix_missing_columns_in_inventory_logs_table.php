<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_logs', 'product_id')) {
                $table->foreignId('product_id')->nullable()->after('inventory_id')->constrained('products')->onDelete('cascade');
            }
            if (!Schema::hasColumn('inventory_logs', 'warehouse_id')) {
                $table->foreignId('warehouse_id')->nullable()->after('product_id')->constrained('warehouses')->onDelete('cascade');
            }
            if (!Schema::hasColumn('inventory_logs', 'change_type')) {
                $table->enum('change_type', ['add', 'remove', 'in', 'out', 'adjust'])->default('add')->after('warehouse_id');
            }
            if (!Schema::hasColumn('inventory_logs', 'reason')) {
                $table->text('reason')->nullable()->after('quantity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn(['product_id', 'warehouse_id', 'change_type', 'reason']);
        });
    }
};
