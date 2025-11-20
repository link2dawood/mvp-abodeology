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
        Schema::create('property_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->enum('document_type', [
                'epc',
                'floorplan',
                'homecheck',
                'consent',
                'planning',
                'building_control',
                'fensa',
                'other'
            ]);
            $table->string('file_path', 255);
            $table->timestamp('uploaded_at')->useCurrent();

            $table->index('property_id');
            $table->index('document_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_documents');
    }
};
