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
        Schema::create('home_page_settings', function (Blueprint $table) {
            $table->id();

            // leidenschaft
            $table->string('section_1_title')->nullable();
            $table->text('section_1_description')->nullable();
            $table->string('section_1_bt_text')->nullable();
            $table->string('section_1_image')->nullable();
            $table->text('section_1_slider_text')->nullable();
            $table->boolean('section_1_status')->default(1);

            // Best selling products
            $table->string('section_2_title')->nullable();
            $table->text('section_2_subtitle')->nullable();
            $table->string('section_2_bt_text')->nullable();
            $table->string('section_2_second_title')->nullable();
            $table->text('section_2_second_subtitle')->nullable();
            $table->boolean('section_2_status')->default(1);

            // Explore Categories
            $table->string('section_3_title')->nullable();
            $table->string('section_3_subtitle')->nullable();
            $table->string('section_3_description')->nullable();
            $table->boolean('section_3_status')->default(1);

            // Our Creations
            $table->string('section_4_title')->nullable();
            $table->string('section_4_subtitle')->nullable();
            $table->boolean('section_4_status')->default(1);

            // Beauty in Imperfection
            $table->string('section_5_title')->nullable();
            $table->string('section_5_description')->nullable();
            $table->boolean('section_5_status')->default(1);

            // Series and Collections
            $table->string('section_6_title')->nullable();
            $table->text('section_6_subtitle')->nullable();
            $table->text('section_6_description')->nullable();
            $table->boolean('section_6_status')->default(1);

            // Custom Design
            $table->string('section_7_title')->nullable();
            $table->text('section_7_subtitle')->nullable();
            $table->text('section_7_description')->nullable();
            $table->string('section_7_bt_text')->nullable();
            $table->string('section_7_img_1')->nullable();
            $table->string('section_7_img_2')->nullable();
            $table->string('section_7_img_3')->nullable();
            $table->string('section_7_img_4')->nullable();
            $table->boolean('section_7_status')->default(1);

            // About us
            $table->string('section_8_title')->nullable();
            $table->text('section_8_description')->nullable();
            $table->string('section_8_img_1')->nullable();
            $table->string('section_8_img_2')->nullable();
            $table->boolean('section_8_status')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_page_settings');
    }
};
