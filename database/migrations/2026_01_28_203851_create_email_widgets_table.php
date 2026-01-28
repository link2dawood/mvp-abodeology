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
        Schema::create('email_widgets', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->index(); // e.g., 'image_basic', 'offer_summary'
            $table->string('name'); // Display name
            $table->string('category')->default('Content'); // Layout, Content, Media, System
            $table->text('html'); // Widget HTML template
            $table->boolean('locked')->default(false); // true = cannot be deleted/edited structurally
            $table->text('description')->nullable(); // Widget description
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_widgets');
    }
};
