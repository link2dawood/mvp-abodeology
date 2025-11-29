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
        Schema::table('offer_decisions', function (Blueprint $table) {
            $table->decimal('counter_amount', 10, 2)->nullable()->after('comments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offer_decisions', function (Blueprint $table) {
            $table->dropColumn('counter_amount');
        });
    }
};
