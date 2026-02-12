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
            $table->decimal('fee_percentage', 5, 2)->nullable()->after('estimated_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('valuations', function (Blueprint $table) {
            $table->dropColumn('fee_percentage');
        });
    }
};
