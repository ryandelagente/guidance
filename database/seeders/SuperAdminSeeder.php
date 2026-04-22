<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@chmsu.edu.ph'],
            [
                'name'      => 'System Administrator',
                'password'  => bcrypt('Admin@CHMSU2026!'),
                'role'      => 'super_admin',
                'is_active' => true,
            ]
        );
    }
}
