<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Standardize offer status from 'rejected' to 'declined'.
     * This migration updates existing offers with 'rejected' status to 'declined'
     * to match the web controller implementation and ensure consistency.
     */
    public function up(): void
    {
        // Update existing offers with 'rejected' status to 'declined'
        $updatedCount = DB::table('offers')
            ->where('status', 'rejected')
            ->update(['status' => 'declined']);
        
        // If status column is an ENUM, we need to modify it
        // Check if column is enum type
        try {
            $columnInfo = DB::select("SHOW COLUMNS FROM offers WHERE Field = 'status'");
            
            if (!empty($columnInfo) && str_contains($columnInfo[0]->Type, 'enum')) {
                // Modify ENUM to replace 'rejected' with 'declined'
                DB::statement("ALTER TABLE offers MODIFY COLUMN status ENUM('pending', 'accepted', 'declined', 'withdrawn', 'countered') DEFAULT 'pending'");
            }
        } catch (\Exception $e) {
            // If column is not ENUM (might be string), that's fine
            Log::info('Offer status column is not ENUM type, skipping ENUM modification');
        }
        
        Log::info("Standardized offer status: Updated {$updatedCount} offers from 'rejected' to 'declined'");
    }

    /**
     * Reverse the migrations.
     * Note: This will change 'declined' back to 'rejected', but this is not recommended.
     */
    public function down(): void
    {
        // Update 'declined' back to 'rejected' (not recommended)
        $updatedCount = DB::table('offers')
            ->where('status', 'declined')
            ->update(['status' => 'rejected']);
        
        // Revert ENUM if it was modified
        try {
            $columnInfo = DB::select("SHOW COLUMNS FROM offers WHERE Field = 'status'");
            
            if (!empty($columnInfo) && str_contains($columnInfo[0]->Type, 'enum')) {
                DB::statement("ALTER TABLE offers MODIFY COLUMN status ENUM('pending', 'accepted', 'rejected', 'withdrawn', 'countered') DEFAULT 'pending'");
            }
        } catch (\Exception $e) {
            Log::info('Could not revert ENUM modification');
        }
        
        Log::info("Reverted offer status: Updated {$updatedCount} offers from 'declined' to 'rejected'");
    }
};
