<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_chats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('support_id');
            $table->unsignedBigInteger('sender_id');
            $table->enum('sender_type', ['customer', 'admin']);
            $table->text('message');
            $table->timestamps();

            $table->foreign('support_id')->references('id')->on('supports')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_chats');
    }
};
