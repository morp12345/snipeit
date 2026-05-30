<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('asset_id');
            $table->foreign('asset_id')->references('id')->on('assets')->cascadeOnDelete();
            $table->unsignedInteger('requested_by');
            $table->foreign('requested_by')->references('id')->on('users')->cascadeOnDelete();
            $table->enum('type', ['repair', 'inspection', 'scheduled_audit']);
            $table->enum('status', ['open', 'in_progress', 'resolved', 'decommissioned'])->default('open');
            $table->text('notes')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->boolean('decommission_needed')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_requests');
    }
};
