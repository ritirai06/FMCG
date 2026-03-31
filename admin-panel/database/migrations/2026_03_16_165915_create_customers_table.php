<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('business_name');
            $table->string('code')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('email')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->boolean('verified')->default(false);
            $table->string('route')->nullable();
            $table->string('group_name')->nullable();
            $table->string('geolocation')->nullable();
            $table->text('billing_address')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('gstin', 20)->nullable();
            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->integer('credit_period')->default(0);
            $table->decimal('credit_limit', 12, 2)->default(0);
            $table->decimal('credit_bill_limit', 12, 2)->default(0);
            $table->string('state_of_supply')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
