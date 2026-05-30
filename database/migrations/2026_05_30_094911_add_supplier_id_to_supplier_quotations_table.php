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
        Schema::table('supplier_quotations', function (Blueprint $table) {
            // Column was partially created as bigint in a failed run — retype it to match suppliers.id (int unsigned)
            $table->unsignedInteger('supplier_id')->nullable()->change();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('supplier_quotations', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn('supplier_id');
        });
    }
};
