<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('unit')->nullable()->after('sku');
            $table->decimal('sell_price', 12, 2)->nullable()->after('unit');
            $table->string('item_code')->nullable()->after('sell_price');
            $table->text('item_description')->nullable()->after('item_code');
            $table->string('hsn_code')->nullable()->after('gst_percent');
            $table->decimal('cess_percent', 5, 2)->default(0)->after('hsn_code');
            $table->decimal('discount', 5, 2)->default(0)->after('cess_percent');
            $table->string('offer_text')->nullable()->after('discount');
            $table->integer('warehouse_id')->nullable()->after('offer_text');
            $table->integer('available_units')->default(0)->after('warehouse_id');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'unit','sell_price','item_code','item_description',
                'hsn_code','cess_percent','discount','offer_text',
                'warehouse_id','available_units',
            ]);
        });
    }
};
