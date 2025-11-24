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
        // Modify the ENUM to add 'awaiting_aml' status
        \DB::statement("ALTER TABLE properties MODIFY COLUMN status ENUM('draft', 'property_details_captured', 'pre_marketing', 'signed', 'awaiting_aml', 'live', 'sstc', 'withdrawn', 'sold') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the awaiting_aml status value
        \DB::statement("ALTER TABLE properties MODIFY COLUMN status ENUM('draft', 'property_details_captured', 'pre_marketing', 'signed', 'live', 'sstc', 'withdrawn', 'sold') DEFAULT 'draft'");
    }
};
