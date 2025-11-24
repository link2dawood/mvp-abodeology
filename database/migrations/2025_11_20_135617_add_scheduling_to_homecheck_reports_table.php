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
        Schema::table('homecheck_reports', function (Blueprint $table) {
            $table->enum('status', ['scheduled', 'in_progress', 'completed'])->default('scheduled')->after('property_id');
            $table->foreignId('scheduled_by')->nullable()->constrained('users')->onDelete('set null')->after('status');
            $table->timestamp('scheduled_date')->nullable()->after('scheduled_by');
            $table->foreignId('completed_by')->nullable()->constrained('users')->onDelete('set null')->after('scheduled_date');
            $table->timestamp('completed_at')->nullable()->after('completed_by');
            $table->text('notes')->nullable()->after('completed_at');
            
            $table->index('status');
            $table->index('scheduled_by');
            $table->index('completed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('homecheck_reports', function (Blueprint $table) {
            $table->dropForeign(['scheduled_by']);
            $table->dropForeign(['completed_by']);
            $table->dropIndex(['status']);
            $table->dropIndex(['scheduled_by']);
            $table->dropIndex(['completed_by']);
            $table->dropColumn([
                'status',
                'scheduled_by',
                'scheduled_date',
                'completed_by',
                'completed_at',
                'notes',
            ]);
        });
    }
};
