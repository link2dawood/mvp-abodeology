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
        // Only add the column if it doesn't already exist, and don't rely on a dropped column
        if (!Schema::hasColumn('viewings', 'viewing_date')) {
            Schema::table('viewings', function (Blueprint $table) {
                // Place viewing_date after pva_id, which always exists on fresh installs
                $table->dateTime('viewing_date')->nullable()->after('pva_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('viewings', 'viewing_date')) {
            Schema::table('viewings', function (Blueprint $table) {
                $table->dropColumn('viewing_date');
            });
        }
    }
};
