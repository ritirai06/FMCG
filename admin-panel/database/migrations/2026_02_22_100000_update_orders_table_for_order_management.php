<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add customer fields if not exist
            if (!Schema::hasColumn('orders', 'customer_name')) {
                $table->string('customer_name')->nullable();
            }
            
            if (!Schema::hasColumn('orders', 'customer_phone')) {
                $table->string('customer_phone')->nullable()->after('customer_name');
            }

            // Add created_by user (sales person who created order)
            if (!Schema::hasColumn('orders', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('sales_person_id')->constrained('users')->nullOnDelete();
            }

            // Add assigned_delivery user (delivery person)
            if (!Schema::hasColumn('orders', 'assigned_delivery')) {
                $table->foreignId('assigned_delivery')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            }

            // Rename amount to total_amount if needed, or keep amount
            if (!Schema::hasColumn('orders', 'total_amount')) {
                $table->decimal('total_amount', 12, 2)->default(0)->after('amount')->nullable();
            }

            // Improve status field with default values
            if (Schema::hasColumn('orders', 'status')) {
                $table->string('status')->default('Pending')->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'customer_phone')) {
                $table->dropColumn('customer_phone');
            }

            if (Schema::hasColumn('orders', 'created_by')) {
                $table->dropConstrainedForeignId('created_by');
            }

            if (Schema::hasColumn('orders', 'assigned_delivery')) {
                $table->dropConstrainedForeignId('assigned_delivery');
            }

            if (Schema::hasColumn('orders', 'total_amount')) {
                $table->dropColumn('total_amount');
            }
        });
    }
};
