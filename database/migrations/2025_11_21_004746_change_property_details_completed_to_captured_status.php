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
        // Update existing records first
        \DB::table('properties')
            ->where('status', 'property_details_completed')
            ->update(['status' => 'property_details_captured']);

        // Modify the ENUM to replace 'property_details_completed' with 'property_details_captured'
        \DB::statement("ALTER TABLE properties MODIFY COLUMN status ENUM('draft', 'property_details_captured', 'pre_marketing', 'signed', 'live', 'sstc', 'withdrawn', 'sold') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Update existing records first
        \DB::table('properties')
            ->where('status', 'property_details_captured')
            ->update(['status' => 'property_details_completed']);

        // Revert the ENUM back
        \DB::statement("ALTER TABLE properties MODIFY COLUMN status ENUM('draft', 'property_details_completed', 'pre_marketing', 'signed', 'live', 'sstc', 'withdrawn', 'sold') DEFAULT 'draft'");
    }
};
