<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'leidenschaft@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('leidenschaft@admin'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
