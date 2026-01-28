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
        Schema::table('email_templates', function (Blueprint $table) {
            $table->string('key')->nullable()->unique()->after('id'); // e.g., 'seller_new_offer', 'viewing_confirmed'
            $table->longText('html_content')->nullable()->after('body'); // Final HTML from GrapesJS
            $table->longText('json_content')->nullable()->after('html_content'); // GrapesJS project data
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->dropColumn(['key', 'html_content', 'json_content']);
        });
    }
};
