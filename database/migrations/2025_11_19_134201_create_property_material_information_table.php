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
        Schema::create('property_material_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->enum('heating_type', ['gas', 'electric', 'oil', 'underfloor', 'other'])->nullable();
            $table->integer('boiler_age_years')->nullable();
            $table->date('boiler_last_serviced')->nullable();
            $table->char('epc_rating', 1)->nullable();
            $table->boolean('gas_supply')->default(false);
            $table->boolean('electricity_supply')->default(false);
            $table->boolean('mains_water')->default(false);
            $table->enum('drainage', ['mains', 'septic_tank', 'private_system'])->nullable();
            $table->text('known_issues')->nullable();
            $table->text('planning_alterations')->nullable();
            $table->boolean('documents_uploaded')->default(false);
            $table->timestamps();

            $table->index('property_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_material_information');
    }
};
