<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('salary_payouts', function (Blueprint $table) {
            $table->id();
            $table->string('employee_name');
            $table->integer('month');
            $table->integer('year');
            $table->decimal('base_salary',12,2)->default(0);
            $table->decimal('allowances',12,2)->default(0);
            $table->decimal('sales',12,2)->default(0);
            $table->decimal('incentive',12,2)->default(0);
            $table->decimal('total_payout',12,2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('salary_payouts');
    }
};
