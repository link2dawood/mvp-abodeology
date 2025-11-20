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
        // MySQL doesn't support modifying enum directly, so we need to use raw SQL
        \DB::statement("ALTER TABLE properties MODIFY COLUMN status ENUM('draft', 'property_details_completed', 'pre_marketing', 'signed', 'live', 'sstc', 'withdrawn', 'sold') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the signed status value
        \DB::statement("ALTER TABLE properties MODIFY COLUMN status ENUM('draft', 'property_details_completed', 'pre_marketing', 'live', 'sstc', 'withdrawn', 'sold') DEFAULT 'draft'");
    }
};
