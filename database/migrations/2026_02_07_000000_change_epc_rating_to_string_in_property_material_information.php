<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Allow EPC rating to store 'awaiting' in addition to A-G.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE property_material_information MODIFY epc_rating VARCHAR(20) NULL');
        } elseif ($driver === 'sqlite') {
            // SQLite: recreate column via table copy (preserve data)
            DB::statement('ALTER TABLE property_material_information RENAME COLUMN epc_rating TO epc_rating_old');
            DB::statement('ALTER TABLE property_material_information ADD COLUMN epc_rating VARCHAR(20) NULL');
            DB::statement('UPDATE property_material_information SET epc_rating = epc_rating_old WHERE epc_rating_old IS NOT NULL');
            DB::statement('ALTER TABLE property_material_information DROP COLUMN epc_rating_old');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE property_material_information MODIFY epc_rating CHAR(1) NULL');
        }
        // SQLite down not fully reversible without data loss for values like 'awaiting'
    }
};
