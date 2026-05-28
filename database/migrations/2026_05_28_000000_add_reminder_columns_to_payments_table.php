<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedTinyInteger('reminder_sent_count')->default(0)->after('meta');
            $table->timestamp('last_reminder_sent_at')->nullable()->after('reminder_sent_count');
            $table->timestamp('reminder_completed_at')->nullable()->after('last_reminder_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['reminder_sent_count', 'last_reminder_sent_at', 'reminder_completed_at']);
        });
    }
};
