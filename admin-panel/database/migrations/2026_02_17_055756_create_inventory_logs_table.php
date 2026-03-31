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
       Schema::create('inventory_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('inventory_id')->nullable()->constrained()->cascadeOnDelete();
    $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('cascade');
    $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->onDelete('cascade');
    $table->enum('change_type', ['add', 'remove', 'in', 'out', 'adjust'])->default('add');
    $table->string('type')->nullable(); // in / out / adjust
    $table->integer('quantity');
    $table->text('reason')->nullable();
    $table->string('note')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
    }
};
