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
        Schema::create('sales_progression', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('offer_id')->constrained('offers')->onDelete('cascade');
            $table->string('solicitor_buyer', 255)->nullable();
            $table->string('solicitor_seller', 255)->nullable();
            $table->boolean('memorandum_of_sale_issued')->default(false);
            $table->boolean('enquiries_raised')->default(false);
            $table->boolean('enquiries_answered')->default(false);
            $table->boolean('searches_ordered')->default(false);
            $table->boolean('searches_received')->default(false);
            $table->date('exchange_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->timestamps();

            $table->index('property_id');
            $table->index('buyer_id');
            $table->index('offer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_progression');
    }
};
