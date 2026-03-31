<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_persons', function (Blueprint $table) {
            $table->decimal('current_latitude', 10, 8)->nullable()->after('avatar_path');
            $table->decimal('current_longitude', 11, 8)->nullable()->after('current_latitude');
            $table->timestamp('last_location_update')->nullable()->after('current_longitude');
            $table->boolean('location_tracking_enabled')->default(true)->after('last_location_update');
        });
    }

    public function down(): void
    {
        Schema::table('sales_persons', function (Blueprint $table) {
            $table->dropColumn(['current_latitude', 'current_longitude', 'last_location_update', 'location_tracking_enabled']);
        });
    }
};
