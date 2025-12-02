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
        Schema::table('offers', function (Blueprint $table) {
            $table->boolean('released_to_seller')->default(false)->after('status');
            $table->timestamp('released_at')->nullable()->after('released_to_seller');
            $table->foreignId('released_by')->nullable()->constrained('users')->onDelete('set null')->after('released_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropForeign(['released_by']);
            $table->dropColumn(['released_to_seller', 'released_at', 'released_by']);
        });
    }
};
