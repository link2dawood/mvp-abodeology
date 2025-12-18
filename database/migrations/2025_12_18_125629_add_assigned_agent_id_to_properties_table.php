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
        Schema::table('properties', function (Blueprint $table) {
            // Add assigned_agent_id for direct agent-property relationship
            // This is the primary agent assigned to manage this property
            $table->foreignId('assigned_agent_id')
                ->nullable()
                ->after('seller_id')
                ->constrained('users')
                ->onDelete('set null')
                ->comment('Primary agent assigned to manage this property');
            
            // Add index for faster queries
            $table->index('assigned_agent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign(['assigned_agent_id']);
            $table->dropIndex(['assigned_agent_id']);
            $table->dropColumn('assigned_agent_id');
        });
    }
};
