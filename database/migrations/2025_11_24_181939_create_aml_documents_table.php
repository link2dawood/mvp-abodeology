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
        Schema::create('aml_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aml_check_id')->constrained('aml_checks')->onDelete('cascade');
            $table->enum('document_type', ['id_document', 'proof_of_address', 'additional'])->default('additional');
            $table->string('file_path', 500);
            $table->string('file_name', 255);
            $table->string('mime_type', 100)->nullable();
            $table->integer('file_size')->nullable(); // in bytes
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('aml_check_id');
            $table->index('document_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aml_documents');
    }
};
