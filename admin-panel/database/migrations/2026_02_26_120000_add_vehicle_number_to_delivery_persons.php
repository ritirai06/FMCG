<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_persons', function (Blueprint $table) {
            $table->string('vehicle_number', 50)->nullable()->after('vehicle');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_persons', function (Blueprint $table) {
            $table->dropColumn('vehicle_number');
        });
    }
};
