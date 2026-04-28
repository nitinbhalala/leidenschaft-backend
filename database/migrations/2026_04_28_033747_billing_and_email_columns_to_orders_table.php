<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('email_news_offers')
                ->default(0)
                ->after('customer_email');

            $table->integer('billing_same_as_shipping')
                ->default(1)
                ->after('postal_code');

            $table->text('billing_address')
                ->nullable()
                ->after('billing_same_as_shipping');

            $table->string('billing_city', 100)
                ->nullable()
                ->after('billing_address');

            $table->string('billing_state', 100)
                ->nullable()
                ->after('billing_city');

            $table->string('billing_country', 100)
                ->nullable()
                ->after('billing_state');

            $table->string('billing_postal_code', 20)
                ->nullable()
                ->after('billing_country');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'email_news_offers',
                'billing_same_as_shipping',
                'billing_address',
                'billing_city',
                'billing_state',
                'billing_country',
                'billing_postal_code',
            ]);
        });
    }
};
