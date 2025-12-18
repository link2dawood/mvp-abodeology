<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add indexes to foreign keys for improved query performance.
     * This migration adds indexes to commonly queried foreign key columns.
     */
    public function up(): void
    {
        // Properties table
        Schema::table('properties', function (Blueprint $table) {
            // Index on assigned_agent_id if column exists
            if (Schema::hasColumn('properties', 'assigned_agent_id')) {
                $table->index('assigned_agent_id', 'idx_properties_assigned_agent_id');
            }
            // Index on status (already exists, but ensure it's there)
            if (!Schema::hasColumn('properties', 'status_indexed')) {
                // Status index should already exist from original migration, but we'll ensure it
                $table->index('status', 'idx_properties_status');
            }
        });

        // Offers table
        Schema::table('offers', function (Blueprint $table) {
            // Index on property_id (if not exists)
            if (!Schema::hasColumn('offers', 'property_id_indexed')) {
                $table->index('property_id', 'idx_offers_property_id');
            }
            // Index on buyer_id (if not exists)
            if (!Schema::hasColumn('offers', 'buyer_id_indexed')) {
                $table->index('buyer_id', 'idx_offers_buyer_id');
            }
            // Index on status (if not exists)
            if (!Schema::hasColumn('offers', 'status_indexed')) {
                $table->index('status', 'idx_offers_status');
            }
        });

        // Viewings table
        Schema::table('viewings', function (Blueprint $table) {
            // Index on property_id (if not exists)
            if (!Schema::hasColumn('viewings', 'property_id_indexed')) {
                $table->index('property_id', 'idx_viewings_property_id');
            }
            // Index on buyer_id (if not exists)
            if (!Schema::hasColumn('viewings', 'buyer_id_indexed')) {
                $table->index('buyer_id', 'idx_viewings_buyer_id');
            }
            // Index on pva_id (if not exists)
            if (Schema::hasColumn('viewings', 'pva_id')) {
                $table->index('pva_id', 'idx_viewings_pva_id');
            }
            // Index on status (if not exists)
            if (!Schema::hasColumn('viewings', 'status_indexed')) {
                $table->index('status', 'idx_viewings_status');
            }
        });

        // Valuations table
        Schema::table('valuations', function (Blueprint $table) {
            // Index on seller_id (if not exists)
            if (!Schema::hasColumn('valuations', 'seller_id_indexed')) {
                $table->index('seller_id', 'idx_valuations_seller_id');
            }
            // Index on agent_id (if not exists)
            if (Schema::hasColumn('valuations', 'agent_id')) {
                $table->index('agent_id', 'idx_valuations_agent_id');
            }
            // Index on status (if not exists)
            if (!Schema::hasColumn('valuations', 'status_indexed')) {
                $table->index('status', 'idx_valuations_status');
            }
        });

        // Property agents pivot table
        if (Schema::hasTable('property_agents')) {
            Schema::table('property_agents', function (Blueprint $table) {
                // Index on property_id (if not exists)
                if (!Schema::hasColumn('property_agents', 'property_id_indexed')) {
                    $table->index('property_id', 'idx_property_agents_property_id');
                }
                // Index on agent_id (if not exists)
                if (!Schema::hasColumn('property_agents', 'agent_id_indexed')) {
                    $table->index('agent_id', 'idx_property_agents_agent_id');
                }
            });
        }

        // Homecheck reports table
        if (Schema::hasTable('homecheck_reports')) {
            Schema::table('homecheck_reports', function (Blueprint $table) {
                // Index on property_id (if not exists)
                if (!Schema::hasColumn('homecheck_reports', 'property_id_indexed')) {
                    $table->index('property_id', 'idx_homecheck_reports_property_id');
                }
                // Index on status (if not exists)
                if (!Schema::hasColumn('homecheck_reports', 'status_indexed')) {
                    $table->index('status', 'idx_homecheck_reports_status');
                }
            });
        }

        // AML checks table
        if (Schema::hasTable('aml_checks')) {
            Schema::table('aml_checks', function (Blueprint $table) {
                // Index on user_id (if not exists)
                if (!Schema::hasColumn('aml_checks', 'user_id_indexed')) {
                    $table->index('user_id', 'idx_aml_checks_user_id');
                }
                // Index on verification_status (if not exists)
                if (!Schema::hasColumn('aml_checks', 'verification_status_indexed')) {
                    $table->index('verification_status', 'idx_aml_checks_verification_status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex('idx_properties_assigned_agent_id');
            $table->dropIndex('idx_properties_status');
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->dropIndex('idx_offers_property_id');
            $table->dropIndex('idx_offers_buyer_id');
            $table->dropIndex('idx_offers_status');
        });

        Schema::table('viewings', function (Blueprint $table) {
            $table->dropIndex('idx_viewings_property_id');
            $table->dropIndex('idx_viewings_buyer_id');
            $table->dropIndex('idx_viewings_pva_id');
            $table->dropIndex('idx_viewings_status');
        });

        Schema::table('valuations', function (Blueprint $table) {
            $table->dropIndex('idx_valuations_seller_id');
            $table->dropIndex('idx_valuations_agent_id');
            $table->dropIndex('idx_valuations_status');
        });

        if (Schema::hasTable('property_agents')) {
            Schema::table('property_agents', function (Blueprint $table) {
                $table->dropIndex('idx_property_agents_property_id');
                $table->dropIndex('idx_property_agents_agent_id');
            });
        }

        if (Schema::hasTable('homecheck_reports')) {
            Schema::table('homecheck_reports', function (Blueprint $table) {
                $table->dropIndex('idx_homecheck_reports_property_id');
                $table->dropIndex('idx_homecheck_reports_status');
            });
        }

        if (Schema::hasTable('aml_checks')) {
            Schema::table('aml_checks', function (Blueprint $table) {
                $table->dropIndex('idx_aml_checks_user_id');
                $table->dropIndex('idx_aml_checks_verification_status');
            });
        }
    }
};
