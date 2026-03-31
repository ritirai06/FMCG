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
        Schema::create('delivery_partner_localities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('delivery_partner_id');
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('locality_id')->nullable();
            $table->timestamps();
            
            $table->foreign('delivery_partner_id')->references('id')->on('delivery_persons')->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->foreign('locality_id')->references('id')->on('localities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_partner_localities');
    }
};
