<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name'         => 'super_admin',
                'display_name' => 'Super Administrator',
                'description'  => 'Full system access including user management and system settings.',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'guidance_director',
                'display_name' => 'Guidance Director / Head',
                'description'  => 'Oversight of all counselors, reports, and department analytics.',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'guidance_counselor',
                'display_name' => 'Guidance Counselor',
                'description'  => 'Access to assigned student profiles, case notes, and referrals.',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'student',
                'display_name' => 'Student',
                'description'  => 'Self-service: appointments, certificates, and online assessments.',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'faculty',
                'display_name' => 'Faculty / Staff',
                'description'  => 'Submit and track student referral forms only.',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ];

        DB::table('roles')->insertOrIgnore($roles);
    }
}
