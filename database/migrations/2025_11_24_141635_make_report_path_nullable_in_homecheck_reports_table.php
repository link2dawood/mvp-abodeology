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
        Schema::table('homecheck_reports', function (Blueprint $table) {
            // Make report_path nullable since it's only set after report generation
            $table->string('report_path', 255)->nullable()->change();
            
            // Add updated_at column if it doesn't exist (Laravel expects it by default)
            if (!Schema::hasColumn('homecheck_reports', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            }
        });
        
        // Convert any empty string report_path values to null
        \DB::table('homecheck_reports')
            ->where('report_path', '')
            ->update(['report_path' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('homecheck_reports', function (Blueprint $table) {
            $table->string('report_path', 255)->nullable(false)->change();
        });
    }
};
