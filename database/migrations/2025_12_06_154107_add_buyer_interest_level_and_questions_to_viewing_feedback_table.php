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
        Schema::table('viewing_feedback', function (Blueprint $table) {
            $table->enum('buyer_interest_level', ['not_interested', 'maybe', 'interested', 'very_interested'])->nullable()->after('viewing_id');
            $table->text('buyer_questions')->nullable()->after('buyer_feedback');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('viewing_feedback', function (Blueprint $table) {
            $table->dropColumn(['buyer_interest_level', 'buyer_questions']);
        });
    }
};
