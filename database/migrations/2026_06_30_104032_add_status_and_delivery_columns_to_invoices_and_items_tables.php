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
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('date');
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->decimal('delivered_qty', 10, 3)->default(0.000)->after('qty');
            $table->decimal('delivered_total_kg', 10, 3)->default(0.000)->after('total_kg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn(['delivered_qty', 'delivered_total_kg']);
        });
    }
};
