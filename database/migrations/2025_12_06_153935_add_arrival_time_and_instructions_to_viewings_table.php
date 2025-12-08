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
        Schema::table('viewings', function (Blueprint $table) {
            $table->timestamp('arrival_time')->nullable()->after('viewing_date');
            $table->text('special_instructions')->nullable()->after('status');
            $table->text('access_instructions')->nullable()->after('special_instructions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('viewings', function (Blueprint $table) {
            $table->dropColumn(['arrival_time', 'special_instructions', 'access_instructions']);
        });
    }
};
