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
        Schema::create('interior', function (Blueprint $table) {
            $table->id();
            // Interior
            $table->string('section_1_title')->nullable();
            $table->text('section_1_subtitle')->nullable();
            $table->text('section_1_side_text')->nullable();
            $table->text('section_1_title_description')->nullable();
            $table->text('section_1_subtitle_description')->nullable();
            $table->string('section_1_image')->nullable();
            $table->boolean('section_1_status')->default(1);
            // Material-Led Design
            $table->string('section_2_title')->nullable();
            $table->text('section_2_subtitle')->nullable();
            $table->text('section_2_title_description')->nullable();
            $table->text('section_2_subtitle_description')->nullable();
            $table->json('section_2_items')->nullable();
            $table->json('section_2_process_steps')->nullable();
            $table->string('section_2_image')->nullable();
            $table->boolean('section_2_status')->default(1);
            // Gallery Section
            $table->string('section_3_title')->nullable();
            $table->string('section_3_side_text')->nullable();
            $table->text('section_3_description')->nullable();
            $table->json('section_3_items')->nullable();

            $table->boolean('section_3_status')->default(1);
            // Showcase Section
            $table->json('section_4_items')->nullable();
            $table->boolean('section_4_status')->default(1);
            // Why Choose Section
            $table->string('section_5_title')->nullable();
            $table->text('section_5_description')->nullable();
            $table->json('section_5_points')->nullable();
            $table->boolean('section_5_status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interior');
    }
};
