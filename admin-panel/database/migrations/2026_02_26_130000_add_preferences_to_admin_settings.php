<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('admin_settings', function (Blueprint $table) {
            $table->boolean('email_notifications')->default(false);
            $table->boolean('dark_mode')->default(false);
            $table->boolean('maintenance_mode')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_settings', function (Blueprint $table) {
            $table->dropColumn(['email_notifications', 'dark_mode', 'maintenance_mode']);
        });
    }
};