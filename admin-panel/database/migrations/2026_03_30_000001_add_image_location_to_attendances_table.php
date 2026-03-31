<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('image_in')->nullable()->after('time_out');
            $table->string('image_out')->nullable()->after('image_in');
            $table->decimal('latitude', 10, 6)->nullable()->after('image_out');
            $table->decimal('longitude', 10, 6)->nullable()->after('latitude');
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['image_in', 'image_out', 'latitude', 'longitude']);
        });
    }
};
