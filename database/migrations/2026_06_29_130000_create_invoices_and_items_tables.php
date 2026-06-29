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
        // جدول الفواتير الرئيسي
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number');
            $table->string('receiver');
            $table->string('sender');
            $table->string('img')->nullable();
            $table->date('date');
            $table->timestamps();
        });

        // جدول محتويات الفاتورة
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->string('code');
            $table->string('type');         // نوع كتابي (نص حر)
            $table->string('fabric_color')->nullable(); // لون القماش
            $table->decimal('qty', 10, 3);  // الكمية (تدعم أعداد عشرية للكيلو/المتر)
            $table->string('unit')->default('كيلو'); // الوحدة: كيلو / متر / قطعة
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
