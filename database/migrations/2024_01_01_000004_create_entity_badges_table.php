<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_badges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('entity_id');
            $table->uuid('badge_id');
            $table->uuid('trigger_id')->nullable();
            $table->enum('awarded_by', ['trigger', 'manual', 'api'])->default('trigger');
            $table->json('metadata')->nullable();
            $table->timestamp('awarded_at')->useCurrent();
            $table->timestamps();

            $table->foreign('entity_id')->references('id')->on('entities')->onDelete('cascade');
            $table->foreign('badge_id')->references('id')->on('badges')->onDelete('cascade');
            $table->foreign('trigger_id')->references('id')->on('triggers')->nullOnDelete();

            $table->unique(['entity_id', 'badge_id']);
            $table->index('badge_id');
            $table->index(['entity_id', 'awarded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity_badges');
    }
};
