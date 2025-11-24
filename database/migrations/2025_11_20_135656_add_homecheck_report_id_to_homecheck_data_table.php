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
        Schema::table('homecheck_data', function (Blueprint $table) {
            $table->foreignId('homecheck_report_id')->nullable()->constrained('homecheck_reports')->onDelete('cascade')->after('property_id');
            $table->boolean('is_360')->default(false)->after('image_path');
            
            $table->index('homecheck_report_id');
            $table->index('is_360');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('homecheck_data', function (Blueprint $table) {
            $table->dropForeign(['homecheck_report_id']);
            $table->dropIndex(['homecheck_report_id']);
            $table->dropIndex(['is_360']);
            $table->dropColumn(['homecheck_report_id', 'is_360']);
        });
    }
};
