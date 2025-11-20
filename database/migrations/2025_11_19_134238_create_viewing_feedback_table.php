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
        Schema::create('viewing_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('viewing_id')->constrained('viewings')->onDelete('cascade');
            $table->enum('buyer_interest', ['high', 'medium', 'low', 'none'])->nullable();
            $table->enum('offer_intent', ['yes', 'maybe', 'no'])->nullable();
            $table->text('feedback_text')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('viewing_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viewing_feedback');
    }
};
