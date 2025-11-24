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
            // Add new fields for PVA feedback
            $table->boolean('buyer_interested')->nullable()->after('viewing_id');
            $table->text('buyer_feedback')->nullable()->after('buyer_interested');
            $table->enum('property_condition', ['excellent', 'good', 'fair', 'poor'])->nullable()->after('buyer_feedback');
            $table->text('buyer_notes')->nullable()->after('property_condition');
            $table->text('pva_notes')->nullable()->after('buyer_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('viewing_feedback', function (Blueprint $table) {
            $table->dropColumn([
                'buyer_interested',
                'buyer_feedback',
                'property_condition',
                'buyer_notes',
                'pva_notes',
            ]);
        });
    }
};
