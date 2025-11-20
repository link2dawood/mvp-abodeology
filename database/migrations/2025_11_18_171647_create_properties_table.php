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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->string('address');
            $table->string('property_type')->nullable();
            $table->decimal('asking_price', 12, 2)->nullable();
            $table->string('status')->default('draft'); // draft, pending, live, sold, withdrawn
            $table->text('description')->nullable();
            $table->json('material_information')->nullable();
            $table->boolean('is_onboarded')->default(false);
            $table->timestamps();

            $table->index(['seller_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
