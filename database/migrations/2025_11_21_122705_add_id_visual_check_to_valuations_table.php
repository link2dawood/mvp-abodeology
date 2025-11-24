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
        Schema::table('valuations', function (Blueprint $table) {
            $table->boolean('id_visual_check')->default(false)->after('notes');
            $table->text('id_visual_check_notes')->nullable()->after('id_visual_check');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('valuations', function (Blueprint $table) {
            $table->dropColumn(['id_visual_check', 'id_visual_check_notes']);
        });
    }
};
