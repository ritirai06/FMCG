<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_persons', function (Blueprint $table) {
            $table->decimal('base_salary', 12, 2)->nullable();
            $table->decimal('allowance', 12, 2)->nullable();
            $table->decimal('bonus_percent', 5, 2)->nullable();
            $table->decimal('target_sales', 12, 2)->nullable();
            $table->decimal('incentive_percent', 5, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('sales_persons', function (Blueprint $table) {
            $table->dropColumn(['base_salary','allowance','bonus_percent','target_sales','incentive_percent']);
        });
    }
};
