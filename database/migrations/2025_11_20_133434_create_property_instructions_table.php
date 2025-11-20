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
        Schema::create('property_instructions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->decimal('fee_percentage', 5, 2)->default(1.5);
            
            // Declarations
            $table->boolean('declaration_accurate')->default(false);
            $table->boolean('declaration_legal_entitlement')->default(false);
            $table->boolean('declaration_immediate_marketing')->default(false);
            $table->boolean('declaration_terms')->default(false);
            $table->boolean('declaration_homecheck')->default(false);
            
            // Seller 1 Signature
            $table->string('seller1_name')->nullable();
            $table->string('seller1_signature')->nullable();
            $table->date('seller1_date')->nullable();
            
            // Seller 2 Signature (optional)
            $table->string('seller2_name')->nullable();
            $table->string('seller2_signature')->nullable();
            $table->date('seller2_date')->nullable();
            
            // Status
            $table->enum('status', ['pending', 'signed', 'declined'])->default('pending');
            
            // Request info
            $table->foreignId('requested_by')->nullable()->constrained('users')->onDelete('set null'); // Agent who requested
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('signed_at')->nullable();
            
            $table->timestamps();
            
            $table->index(['property_id', 'status']);
            $table->index('seller_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_instructions');
    }
};
