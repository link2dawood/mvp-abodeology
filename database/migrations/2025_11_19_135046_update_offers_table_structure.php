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
        // Try to drop foreign key if it exists
        try {
            Schema::table('offers', function (Blueprint $table) {
                $table->dropForeign(['responded_by']);
            });
        } catch (\Exception $e) {
            // Foreign key doesn't exist, continue
        }

        // Check which columns exist and drop them if they do
        $columns = Schema::getColumnListing('offers');
        $columnsToDrop = [];

        foreach (['amount', 'notes', 'conditions', 'submitted_at', 'responded_at', 'responded_by', 'status'] as $column) {
            if (in_array($column, $columns)) {
                $columnsToDrop[] = $column;
            }
        }

        if (!empty($columnsToDrop)) {
            Schema::table('offers', function (Blueprint $table) use ($columnsToDrop) {
                $table->dropColumn($columnsToDrop);
            });
        }

        // Add new columns if they don't already exist
        $existingColumns = Schema::getColumnListing('offers');

        Schema::table('offers', function (Blueprint $table) use ($existingColumns) {
            if (!in_array('offer_amount', $existingColumns)) {
                $table->decimal('offer_amount', 12, 2)->after('buyer_id');
            }
            if (!in_array('deposit_amount', $existingColumns)) {
                $table->decimal('deposit_amount', 12, 2)->nullable()->after('offer_amount');
            }
            if (!in_array('funding_type', $existingColumns)) {
                $table->enum('funding_type', ['cash', 'mortgage', 'part_mortgage'])->nullable()->after('deposit_amount');
            }
            if (!in_array('aip_status', $existingColumns)) {
                $table->enum('aip_status', ['provided', 'not_provided'])->default('not_provided')->after('funding_type');
            }
            if (!in_array('chain_position', $existingColumns)) {
                $table->string('chain_position', 255)->nullable()->after('aip_status');
            }
            if (!in_array('conditions', $existingColumns)) {
                $table->text('conditions')->nullable()->after('chain_position');
            }
            if (!in_array('status', $existingColumns)) {
                $table->enum('status', ['pending', 'accepted', 'declined', 'withdrawn', 'countered'])->default('pending')->after('conditions');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn(['offer_amount', 'deposit_amount', 'funding_type', 'aip_status', 'chain_position', 'conditions', 'status']);
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->decimal('amount', 12, 2)->after('buyer_id');
            $table->string('status')->default('pending')->after('amount');
            $table->text('notes')->nullable()->after('status');
            $table->json('conditions')->nullable()->after('notes');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->onDelete('set null');
        });
    }
};
