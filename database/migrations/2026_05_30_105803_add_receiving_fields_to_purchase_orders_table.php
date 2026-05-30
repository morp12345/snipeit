<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->unsignedInteger('asset_id')->nullable()->after('created_by');
            $table->foreign('asset_id')->references('id')->on('assets')->nullOnDelete();
            $table->timestamp('received_at')->nullable()->after('asset_id');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['asset_id']);
            $table->dropColumn(['asset_id', 'received_at']);
        });
    }
};
