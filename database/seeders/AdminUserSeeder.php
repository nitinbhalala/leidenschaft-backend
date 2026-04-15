<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserToken;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::insert([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'leidenschaft@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('leidenschaft@admin'),
            'is_admin' => 1,
            'remember_token' => Str::random(30),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        UserToken::insert([
            'user_id' => 1,
            'token' => Str::random(64),
            'token_expires_at' => now()->addDays(7),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
