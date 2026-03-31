<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'store_id')) {
                $table->foreignId('store_id')->nullable()->after('order_number')->constrained('stores')->nullOnDelete();
            }

            if (!Schema::hasColumn('orders', 'sales_person_id')) {
                $table->foreignId('sales_person_id')->nullable()->after('store_id')->constrained('sales_persons')->nullOnDelete();
            }

            if (!Schema::hasColumn('orders', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'sales_person_id')) {
                $table->dropConstrainedForeignId('sales_person_id');
            }

            if (Schema::hasColumn('orders', 'store_id')) {
                $table->dropConstrainedForeignId('store_id');
            }

            if (Schema::hasColumn('orders', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};

