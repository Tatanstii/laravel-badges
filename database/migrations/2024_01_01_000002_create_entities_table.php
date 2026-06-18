<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('external_id');
            $table->string('display_name')->nullable();
            $table->string('avatar_url', 512)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique('external_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entities');
    }
};
