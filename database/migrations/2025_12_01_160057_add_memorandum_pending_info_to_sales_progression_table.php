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
        Schema::table('sales_progression', function (Blueprint $table) {
            $table->boolean('memorandum_pending_info')->default(false)->after('memorandum_of_sale_issued');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_progression', function (Blueprint $table) {
            $table->dropColumn('memorandum_pending_info');
        });
    }
};
