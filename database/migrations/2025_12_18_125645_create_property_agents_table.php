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
        Schema::create('property_agents', function (Blueprint $table) {
            $table->id();
            
            // Property and Agent relationship
            $table->foreignId('property_id')
                ->constrained('properties')
                ->onDelete('cascade');
            
            $table->foreignId('agent_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('User with role agent or admin');
            
            // Track assignment details
            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('User who assigned this agent (usually admin)');
            
            $table->timestamp('assigned_at')->useCurrent();
            $table->boolean('is_primary')->default(false)->comment('Primary agent for this property');
            $table->text('notes')->nullable()->comment('Assignment notes');
            
            $table->timestamps();
            
            // Ensure one property can have multiple agents, but prevent duplicates
            $table->unique(['property_id', 'agent_id']);
            
            // Indexes for performance
            $table->index('property_id');
            $table->index('agent_id');
            $table->index(['property_id', 'is_primary']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_agents');
    }
};
