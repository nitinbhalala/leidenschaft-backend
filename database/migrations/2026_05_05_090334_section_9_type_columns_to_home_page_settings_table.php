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
        Schema::table('home_page_settings', function (Blueprint $table) {
            $table->string('section_9_title')->nullable()->after('section_8_status');
            $table->string('section_9_subtitle')->nullable()->after('section_9_title');
            $table->boolean('section_9_status')->default(1)->after('section_9_subtitle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_page_settings', function (Blueprint $table) {
            $table->dropColumn('section_9_title');
            $table->dropColumn('section_9_subtitle');
            $table->dropColumn('section_9_status');
        });
    }
};
