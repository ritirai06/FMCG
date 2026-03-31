<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_person_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_person_id')->constrained('sales_persons')->onDelete('cascade');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('address')->nullable();
            $table->string('activity_type')->nullable(); // visit, check-in, check-out, travel
            $table->text('notes')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();
            
            $table->index(['sales_person_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_person_locations');
    }
};
