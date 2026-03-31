<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Extend invoices table
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'party_id')) {
                $table->unsignedBigInteger('party_id')->nullable()->after('order_id');
            }
            if (!Schema::hasColumn('invoices', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('party_id');
            }
            if (!Schema::hasColumn('invoices', 'assigned_delivery')) {
                $table->unsignedBigInteger('assigned_delivery')->nullable()->after('created_by');
            }
            if (!Schema::hasColumn('invoices', 'tax')) {
                $table->decimal('tax', 10, 2)->default(0)->after('amount');
            }
            if (!Schema::hasColumn('invoices', 'discount')) {
                $table->decimal('discount', 10, 2)->default(0)->after('tax');
            }
            if (!Schema::hasColumn('invoices', 'due_date')) {
                $table->date('due_date')->nullable()->after('date');
            }
            if (!Schema::hasColumn('invoices', 'notes')) {
                $table->text('notes')->nullable()->after('discount');
            }
        });

        // Create invoice_items table
        if (!Schema::hasTable('invoice_items')) {
            Schema::create('invoice_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('invoice_id');
                $table->unsignedBigInteger('item_id')->nullable();
                $table->string('item_name')->nullable();
                $table->decimal('quantity', 10, 2)->default(1);
                $table->decimal('price', 10, 2)->default(0);
                $table->decimal('total', 10, 2)->default(0);
                $table->timestamps();

                $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('invoices', 'party_id') ? 'party_id' : null,
                Schema::hasColumn('invoices', 'created_by') ? 'created_by' : null,
                Schema::hasColumn('invoices', 'assigned_delivery') ? 'assigned_delivery' : null,
                Schema::hasColumn('invoices', 'tax') ? 'tax' : null,
                Schema::hasColumn('invoices', 'discount') ? 'discount' : null,
                Schema::hasColumn('invoices', 'due_date') ? 'due_date' : null,
                Schema::hasColumn('invoices', 'notes') ? 'notes' : null,
            ]));
        });
    }
};
