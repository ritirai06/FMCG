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
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('sku')->unique();
        $table->string('brand');
        $table->string('category');
        $table->string('sub_category')->nullable();
        $table->decimal('purchase_price', 10, 2);
        $table->decimal('sale_price', 10, 2);
        $table->decimal('mrp', 10, 2);
        $table->decimal('margin', 10, 2);
        $table->string('status')->default('Active');
        $table->string('image')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
