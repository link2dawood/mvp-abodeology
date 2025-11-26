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
            $table->string('seller2_name', 255)->nullable()->after('seller_id');
            $table->string('seller2_email', 255)->nullable()->after('seller2_name');
            $table->string('seller2_phone', 20)->nullable()->after('seller2_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'seller2_name',
                'seller2_email',
                'seller2_phone',
            ]);
        });
    }
};
