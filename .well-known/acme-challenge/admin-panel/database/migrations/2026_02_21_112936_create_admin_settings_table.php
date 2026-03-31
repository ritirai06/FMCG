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
    Schema::create('admin_settings', function (Blueprint $table) {
        $table->id();

        // Admin
        $table->string('name')->nullable();
        $table->string('email')->nullable();
        $table->string('phone')->nullable();
        $table->string('profile_image')->nullable();

        // Company
        $table->string('company_name')->nullable();
        $table->string('gst_number')->nullable();
        $table->string('company_email')->nullable();
        $table->string('company_phone')->nullable();
        $table->text('company_address')->nullable();

        // Preferences
        $table->string('currency')->nullable();
        $table->string('language')->nullable();
        $table->string('timezone')->nullable();

        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_settings');
    }
};
