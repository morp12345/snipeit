<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_quotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_id');
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->cascadeOnDelete();
            $table->string('supplier_name');
            $table->string('supplier_email')->nullable();
            $table->decimal('price', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->unsignedInteger('lead_time_days')->nullable();
            $table->unsignedInteger('warranty_months')->nullable();
            $table->text('compliance_notes')->nullable();
            $table->string('document_path')->nullable();
            $table->boolean('is_awarded')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_quotations');
    }
};
