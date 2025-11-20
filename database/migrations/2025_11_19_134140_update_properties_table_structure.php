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
            // Change address to TEXT
            $table->text('address')->change();
            
            // Add new columns
            $table->string('postcode', 20)->nullable()->after('address');
            
            // Change property_type to ENUM
            $table->enum('property_type', ['detached', 'semi', 'terraced', 'flat', 'maisonette', 'bungalow', 'other'])->nullable()->change();
            
            $table->integer('bedrooms')->nullable()->after('property_type');
            $table->integer('bathrooms')->nullable()->after('bedrooms');
            
            $table->enum('parking', ['none', 'on_street', 'driveway', 'garage', 'allocated', 'permit'])->nullable()->after('bathrooms');
            
            $table->enum('tenure', ['freehold', 'leasehold', 'share_freehold', 'unknown'])->nullable()->after('parking');
            $table->integer('lease_years_remaining')->nullable()->after('tenure');
            $table->decimal('ground_rent', 10, 2)->nullable()->after('lease_years_remaining');
            $table->decimal('service_charge', 10, 2)->nullable()->after('ground_rent');
            $table->string('managing_agent', 255)->nullable()->after('service_charge');
            
            // Change status to ENUM
            $table->enum('status', ['draft', 'pre_marketing', 'live', 'sstc', 'withdrawn', 'sold'])->default('draft')->change();
            
            // Remove old columns that will be moved to separate tables
            $table->dropColumn(['material_information', 'is_onboarded']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('address')->change();
            $table->dropColumn([
                'postcode',
                'bedrooms',
                'bathrooms',
                'parking',
                'tenure',
                'lease_years_remaining',
                'ground_rent',
                'service_charge',
                'managing_agent',
            ]);
            $table->string('property_type')->nullable()->change();
            $table->string('status')->default('draft')->change();
            $table->json('material_information')->nullable();
            $table->boolean('is_onboarded')->default(false);
        });
    }
};
