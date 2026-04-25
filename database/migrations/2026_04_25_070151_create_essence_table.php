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
        Schema::create('essence', function (Blueprint $table) {
            $table->id();
            $table->string('section_1_title')->nullable();
            $table->text('section_1_subtitle')->nullable();
            $table->text('section_1_description')->nullable();
            $table->string('section_1_image')->nullable();
            $table->boolean('section_1_status')->default(1);
            $table->string('section_2_title')->nullable();
            $table->text('section_2_subtitle')->nullable();
            $table->text('section_2_description')->nullable();
            $table->string('section_2_image')->nullable();
            $table->boolean('section_2_status')->default(1);
            $table->string('section_3_title')->nullable();
            $table->text('section_3_subtitle')->nullable();
            $table->text('section_3_description')->nullable();
            $table->string('section_3_image')->nullable();
            $table->boolean('section_3_status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('essence');
    }
};
