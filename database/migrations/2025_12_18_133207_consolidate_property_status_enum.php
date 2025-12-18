<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Consolidate property status ENUM to a single definitive definition.
     * 
     * This migration ensures all valid property statuses are defined in one place,
     * resolving inconsistencies from multiple migrations that modified the ENUM.
     * 
     * Valid Statuses (in workflow order):
     * - draft: Initial state, property being created
     * - property_details_captured: Property details captured during valuation
     * - pre_marketing: Property ready for marketing but not yet live
     * - signed: Terms & Conditions signed by seller
     * - awaiting_aml: Waiting for AML documents from seller
     * - live: Property is live on the market
     * - sstc: Sold Subject to Contract
     * - withdrawn: Property withdrawn from market
     * - sold: Property has been sold (final state)
     */
    public function up(): void
    {
        // First, update any legacy status values
        // Handle 'property_details_completed' if it exists (should have been migrated, but just in case)
        $legacyCount = DB::table('properties')
            ->where('status', 'property_details_completed')
            ->count();
        
        if ($legacyCount > 0) {
            DB::table('properties')
                ->where('status', 'property_details_completed')
                ->update(['status' => 'property_details_captured']);
            
            Log::info("Updated {$legacyCount} properties from 'property_details_completed' to 'property_details_captured'");
        }
        
        // Consolidate ENUM to definitive list of all valid statuses
        // This ensures consistency regardless of migration order
        try {
            DB::statement("ALTER TABLE properties MODIFY COLUMN status ENUM(
                'draft',
                'property_details_captured',
                'pre_marketing',
                'signed',
                'awaiting_aml',
                'live',
                'sstc',
                'withdrawn',
                'sold'
            ) DEFAULT 'draft'");
            
            Log::info('Property status ENUM consolidated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to consolidate property status ENUM: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     * Note: This reverts to a previous state, but exact state depends on migration history.
     */
    public function down(): void
    {
        // Revert to state before consolidation
        // This includes 'awaiting_aml' as it was in the last migration
        try {
            DB::statement("ALTER TABLE properties MODIFY COLUMN status ENUM(
                'draft',
                'property_details_captured',
                'pre_marketing',
                'signed',
                'awaiting_aml',
                'live',
                'sstc',
                'withdrawn',
                'sold'
            ) DEFAULT 'draft'");
        } catch (\Exception $e) {
            Log::error('Failed to revert property status ENUM: ' . $e->getMessage());
        }
    }
};
