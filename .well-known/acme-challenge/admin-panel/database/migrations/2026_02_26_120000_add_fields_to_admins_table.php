<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            if (!Schema::hasColumn('admins', 'name')) {
                $table->string('name')->nullable();
            }
            if (!Schema::hasColumn('admins', 'email')) {
                $table->string('email')->unique()->nullable(false);
            }
            if (!Schema::hasColumn('admins', 'password')) {
                $table->string('password');
            }
            if (!Schema::hasColumn('admins', 'role')) {
                $table->string('role')->default('admin');
            }
            if (!Schema::hasColumn('admins', 'remember_token')) {
                $table->rememberToken();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            if (Schema::hasColumn('admins', 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('admins', 'email')) {
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('admins', 'password')) {
                $table->dropColumn('password');
            }
            if (Schema::hasColumn('admins', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
        });
    }
};
