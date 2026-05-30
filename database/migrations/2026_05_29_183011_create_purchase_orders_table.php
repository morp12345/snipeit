<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_request_id');
            $table->foreign('purchase_request_id')->references('id')->on('purchase_requests')->cascadeOnDelete();
            $table->string('po_number')->unique();
            $table->unsignedInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
            $table->enum('status', ['draft', 'sent', 'received', 'closed'])->default('draft');
            $table->decimal('total_amount', 15, 2)->default(0.00);
            $table->string('currency', 3)->default('USD');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
