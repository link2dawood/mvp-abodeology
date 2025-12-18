<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Backfill agent property assignments from PropertyInstruction.requested_by.
     * This migration ensures existing agent-property relationships are preserved
     * when migrating to the new assignment system.
     */
    public function up(): void
    {
        // Get all PropertyInstructions with requested_by set (agent assignments)
        $instructions = \DB::table('property_instructions')
            ->whereNotNull('requested_by')
            ->get();
        
        foreach ($instructions as $instruction) {
            $propertyId = $instruction->property_id;
            $agentId = $instruction->requested_by;
            
            // Verify agent exists and has correct role
            $agent = \DB::table('users')
                ->where('id', $agentId)
                ->whereIn('role', ['admin', 'agent'])
                ->first();
            
            if (!$agent) {
                \Log::warning("Skipping backfill: User ID {$agentId} is not a valid agent/admin for property {$propertyId}");
                continue;
            }
            
            // Update property.assigned_agent_id (if not already set)
            \DB::table('properties')
                ->where('id', $propertyId)
                ->whereNull('assigned_agent_id')
                ->update(['assigned_agent_id' => $agentId]);
            
            // Add to property_agents pivot table (if not already exists)
            \DB::table('property_agents')->updateOrInsert(
                [
                    'property_id' => $propertyId,
                    'agent_id' => $agentId,
                ],
                [
                    'assigned_by' => $agentId, // Assume self-assigned if no record of who assigned
                    'assigned_at' => $instruction->requested_at ?? $instruction->created_at ?? now(),
                    'is_primary' => true,
                    'notes' => 'Migrated from PropertyInstruction.requested_by',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
        
        \Log::info("Backfilled agent property assignments: " . $instructions->count() . " assignments processed");
    }

    /**
     * Reverse the migrations.
     * Note: This does not delete the new assignments, just logs a warning.
     * Manual cleanup would be required if needed.
     */
    public function down(): void
    {
        // Don't delete data on rollback - just log
        \Log::warning("Backfill migration rolled back. Manual cleanup of property_agents table may be required.");
    }
};
