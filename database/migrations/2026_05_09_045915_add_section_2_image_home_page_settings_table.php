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
            $table->string('section_2_image')->nullable()->after('section_2_second_subtitle');
            $table->json('section_2_product_ids')->nullable()->after('section_2_image');
            $table->json('section_2_product_position')->nullable()->after('section_2_product_ids');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_page_settings', function (Blueprint $table) {
            $table->dropColumn('section_2_image');
            $table->dropColumn('section_2_product_ids');
            $table->dropColumn('section_2_product_position');
        });
    }
};
