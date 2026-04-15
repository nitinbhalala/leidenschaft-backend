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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('sub_category_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();
            $table->string('name');
            $table->string('slug')->nullable()->unique();
            $table->string('sku')->nullable()->unique();
            $table->text('description')->nullable();
            $table->text('delivery_returns')->nullable();
            $table->text('material_dimensions')->nullable();
            $table->integer('stock')->default(0);
            $table->decimal('price', 10, 2);
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
