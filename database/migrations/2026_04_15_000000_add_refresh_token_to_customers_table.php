<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('refresh_token', 128)->nullable()->after('token_expires_at');
            $table->timestamp('refresh_token_expires_at')->nullable()->after('refresh_token');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['refresh_token', 'refresh_token_expires_at']);
        });
    }
};
