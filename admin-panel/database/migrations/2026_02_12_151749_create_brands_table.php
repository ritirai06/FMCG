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
    Schema::create('brands', function (Blueprint $table) {
        $table->id();

        $table->string('name');                 // Brand Name
        $table->string('slug')->unique();       // SEO slug
        $table->text('description')->nullable(); // Description
        $table->string('logo')->nullable();     // Logo image path
        $table->enum('status', ['Active','Inactive'])
              ->default('Active');

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
