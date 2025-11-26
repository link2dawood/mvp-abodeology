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
            $table->integer('reception_rooms')->nullable()->after('bathrooms');
            $table->string('outbuildings', 500)->nullable()->after('reception_rooms');
            $table->text('garden_details')->nullable()->after('outbuildings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'reception_rooms',
                'outbuildings',
                'garden_details',
            ]);
        });
    }
};
