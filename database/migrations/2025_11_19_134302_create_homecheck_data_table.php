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
        Schema::create('homecheck_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->string('room_name', 255);
            $table->string('image_path', 255);
            $table->decimal('moisture_reading', 6, 2)->nullable();
            $table->integer('ai_rating')->nullable();
            $table->text('ai_comments')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('property_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homecheck_data');
    }
};
