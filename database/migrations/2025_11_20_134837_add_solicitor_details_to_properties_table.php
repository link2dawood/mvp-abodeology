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
            $table->string('solicitor_name', 255)->nullable()->after('managing_agent');
            $table->string('solicitor_firm', 255)->nullable()->after('solicitor_name');
            $table->string('solicitor_email', 255)->nullable()->after('solicitor_firm');
            $table->string('solicitor_phone', 20)->nullable()->after('solicitor_email');
            $table->boolean('solicitor_details_completed')->default(false)->after('solicitor_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'solicitor_name',
                'solicitor_firm',
                'solicitor_email',
                'solicitor_phone',
                'solicitor_details_completed',
            ]);
        });
    }
};
