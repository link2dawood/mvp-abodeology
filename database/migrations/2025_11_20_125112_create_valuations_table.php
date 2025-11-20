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
        Schema::create('valuations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->string('property_address');
            $table->string('postcode')->nullable();
            $table->string('property_type')->nullable(); // detached, semi, terraced, flat, etc.
            $table->integer('bedrooms')->nullable();
            $table->decimal('estimated_value', 12, 2)->nullable(); // Valuation result
            $table->date('valuation_date')->nullable(); // Scheduled date
            $table->time('valuation_time')->nullable(); // Scheduled time
            $table->enum('status', ['pending', 'scheduled', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->text('seller_notes')->nullable(); // Notes from seller during booking
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('valuations');
    }
};
