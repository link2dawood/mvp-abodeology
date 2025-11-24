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
        Schema::create('property_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->string('file_path', 255);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->string('caption', 500)->nullable();
            $table->timestamp('uploaded_at')->useCurrent();

            $table->index('property_id');
            $table->index(['property_id', 'sort_order']);
            $table->index('is_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_photos');
    }
};
