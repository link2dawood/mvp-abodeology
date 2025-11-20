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
        // Check which columns and indexes exist
        $columns = Schema::getColumnListing('viewings');
        $columnsToDrop = [];
        
        foreach (['scheduled_at', 'pva_feedback', 'pva_notes', 'completed_at', 'status'] as $column) {
            if (in_array($column, $columns)) {
                $columnsToDrop[] = $column;
            }
        }

        // Try to drop indexes if they exist
        try {
            Schema::table('viewings', function (Blueprint $table) {
                $table->dropIndex(['property_id', 'scheduled_at']);
            });
        } catch (\Exception $e) {
            // Index doesn't exist, continue
        }

        try {
            Schema::table('viewings', function (Blueprint $table) {
                $table->dropIndex(['pva_id', 'scheduled_at']);
            });
        } catch (\Exception $e) {
            // Index doesn't exist, continue
        }

        try {
            Schema::table('viewings', function (Blueprint $table) {
                $table->dropIndex(['scheduled_at']);
            });
        } catch (\Exception $e) {
            // Index doesn't exist, continue
        }

        // Drop columns if they exist
        if (!empty($columnsToDrop)) {
            Schema::table('viewings', function (Blueprint $table) use ($columnsToDrop) {
                $table->dropColumn($columnsToDrop);
            });
        }

        // Add new columns if they don't already exist
        $existingColumns = Schema::getColumnListing('viewings');

        Schema::table('viewings', function (Blueprint $table) use ($existingColumns) {
            if (!in_array('viewing_date', $existingColumns)) {
                $table->dateTime('viewing_date')->after('pva_id');
            }
            if (!in_array('status', $existingColumns)) {
                $table->enum('status', ['scheduled', 'completed', 'no_show', 'cancelled'])->default('scheduled')->after('viewing_date');
            }
        });

        // Add new indexes if they don't exist
        $indexes = Schema::getConnection()->select("SHOW INDEXES FROM viewings");
        $indexNames = array_column($indexes, 'Key_name');

        Schema::table('viewings', function (Blueprint $table) use ($indexNames) {
            if (!in_array('viewings_property_id_viewing_date_index', $indexNames)) {
                $table->index(['property_id', 'viewing_date']);
            }
            if (!in_array('viewings_pva_id_viewing_date_index', $indexNames)) {
                $table->index(['pva_id', 'viewing_date']);
            }
            if (!in_array('viewings_viewing_date_index', $indexNames)) {
                $table->index('viewing_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('viewings', function (Blueprint $table) {
            $table->dropColumn(['viewing_date', 'status']);
            $table->dropIndex(['property_id', 'viewing_date']);
            $table->dropIndex(['pva_id', 'viewing_date']);
            $table->dropIndex(['viewing_date']);
        });

        Schema::table('viewings', function (Blueprint $table) {
            $table->dateTime('scheduled_at')->after('pva_id');
            $table->string('status')->default('scheduled')->after('scheduled_at');
            $table->text('pva_feedback')->nullable()->after('status');
            $table->json('pva_notes')->nullable()->after('pva_feedback');
            $table->timestamp('completed_at')->nullable()->after('pva_notes');
            
            $table->index(['property_id', 'scheduled_at']);
            $table->index(['pva_id', 'scheduled_at']);
            $table->index('scheduled_at');
        });
    }
};
