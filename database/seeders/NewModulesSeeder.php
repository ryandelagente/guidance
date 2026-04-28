<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\AuditLog;
use App\Models\StudentProfile;
use App\Models\User;
use App\Models\WellnessCheckin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class NewModulesSeeder extends Seeder
{
    public function run(): void
    {
        $director = User::where('role', 'guidance_director')->first();
        $counselors = User::where('role', 'guidance_counselor')->where('is_active', true)->get();
        $students = StudentProfile::with('user')->get();
        $author = $director ?: $counselors->first() ?: User::where('role', 'super_admin')->first();

        if (!$author) {
            $this->command?->warn('No staff users found — run base seeders first.');
            return;
        }

        $this->seedAnnouncements($author, $counselors);
        $this->seedWellnessCheckins($students, $counselors);
        $this->seedAuditLogs($students, $counselors);

        $this->command?->info('New modules sample data seeded.');
    }

    private function seedAnnouncements(User $author, $counselors): void
    {
        $items = [
            [
                'title'    => 'Welcome to the New Guidance Management System',
                'body'     => "We are pleased to launch the new CHMSU Guidance Management System (GMS).\n\nThis platform makes it easier for you to:\n• Book counseling appointments online\n• Submit wellness check-ins\n• Receive timely announcements\n• Access your records securely\n\nIf you have feedback or encounter issues, please reach out to the Guidance Office.",
                'audience' => 'all',
                'priority' => 'info',
                'is_pinned'=> true,
                'published_at' => now()->subDays(14),
            ],
            [
                'title'    => 'Mental Health Awareness Week — May 5-9',
                'body'     => "Join us for Mental Health Awareness Week with daily activities:\n\n• Mon: Free wellness screening\n• Tue: Stress management workshop\n• Wed: Open forum on academic anxiety\n• Thu: Self-care art session\n• Fri: Meet your counselor day\n\nAll events are open to all students. See you there!",
                'audience' => 'students',
                'priority' => 'info',
                'is_pinned'=> true,
                'published_at' => now()->subDays(7),
                'expires_at'   => now()->addDays(20),
            ],
            [
                'title'    => 'Final Exam Schedule Reminder',
                'body'     => "Final examinations begin May 15. Counseling appointments will have priority slots reserved for crisis support.\n\nTake care of yourselves — sleep, eat well, and reach out if you need support.",
                'audience' => 'students',
                'priority' => 'warning',
                'published_at' => now()->subDays(3),
                'expires_at'   => now()->addDays(15),
            ],
            [
                'title'    => 'Urgent: System Maintenance — May 1, 10 PM to 12 AM',
                'body'     => "The GMS will be unavailable for 2 hours due to scheduled maintenance.\n\nPlan accordingly — submit any urgent requests before 9 PM.",
                'audience' => 'all',
                'priority' => 'urgent',
                'published_at' => now()->subDays(1),
                'expires_at'   => now()->addDays(3),
            ],
            [
                'title'    => 'Q3 Counselor Meeting — Conference Room B',
                'body'     => "Quarterly review meeting on Friday, 2 PM. Please prepare:\n\n• Caseload summary\n• Outstanding referrals\n• Notable cases for group discussion\n\nLight snacks will be provided.",
                'audience' => 'counselors',
                'priority' => 'info',
                'published_at' => now()->subDays(2),
                'expires_at'   => now()->addDays(5),
            ],
            [
                'title'    => 'Faculty Referral Training Module',
                'body'     => "All faculty are invited to a 1-hour training session on the new referral workflow.\n\nLearn how to submit, track, and follow up on student referrals through the GMS.",
                'audience' => 'faculty',
                'priority' => 'info',
                'published_at' => now()->subDays(5),
            ],
        ];

        foreach ($items as $item) {
            Announcement::create(array_merge($item, [
                'created_by'   => $counselors->random()->id ?? $author->id,
                'is_published' => true,
            ]));
        }
    }

    private function seedWellnessCheckins($students, $counselors): void
    {
        if ($students->isEmpty()) return;

        // Profiles for varied check-in patterns
        $patterns = [
            'doing_well'  => ['mood' => [4,5], 'stress' => [1,2], 'sleep' => [4,5], 'academic' => [1,2], 'wants' => false],
            'mid_stress'  => ['mood' => [3,4], 'stress' => [3,4], 'sleep' => [2,3], 'academic' => [3,4], 'wants' => false],
            'struggling'  => ['mood' => [1,2], 'stress' => [4,5], 'sleep' => [1,2], 'academic' => [4,5], 'wants' => true],
            'crisis'      => ['mood' => [1,2], 'stress' => [5,5], 'sleep' => [1,2], 'academic' => [5,5], 'wants' => true],
        ];

        $notes = [
            'doing_well' => ['Pretty good week so far.', 'Feeling motivated.', null, null, 'Caught up on sleep.'],
            'mid_stress' => ['Lots to juggle but managing.', 'Project deadlines piling up.', null, 'Group work is stressful.'],
            'struggling' => ['I feel overwhelmed.', 'Hard to focus on anything.', 'Family issues at home.', 'Behind on every subject.'],
            'crisis'     => ['I really need to talk to someone.', 'Can\'t cope anymore.', 'Please someone help me figure this out.'],
        ];

        // Each student gets 1-5 check-ins over the past 14 days
        foreach ($students as $student) {
            $count = rand(1, 5);
            $patternKey = collect(array_keys($patterns))->random();
            // 20% chance student is currently in crisis
            if (rand(1, 100) <= 20) $patternKey = 'crisis';

            $pattern = $patterns[$patternKey];

            for ($i = 0; $i < $count; $i++) {
                $daysAgo = rand(0, 14);
                $createdAt = Carbon::now()->subDays($daysAgo)->setTime(rand(7, 20), rand(0, 59));

                $wantsCounselor = $pattern['wants'] && rand(1, 100) <= 70;
                $reviewed = !$wantsCounselor && rand(1, 100) <= 60;

                WellnessCheckin::create([
                    'student_profile_id' => $student->id,
                    'mood'               => rand(...$pattern['mood']),
                    'stress_level'       => rand(...$pattern['stress']),
                    'sleep_quality'      => rand(...$pattern['sleep']),
                    'academic_stress'    => rand(...$pattern['academic']),
                    'notes'              => collect($notes[$patternKey])->random(),
                    'wants_counselor'    => $wantsCounselor,
                    'reviewed'           => $reviewed,
                    'reviewed_by'        => $reviewed ? $counselors->random()->id ?? null : null,
                    'reviewed_at'        => $reviewed ? $createdAt->copy()->addHours(rand(2, 24)) : null,
                    'created_at'         => $createdAt,
                    'updated_at'         => $createdAt,
                ]);
            }
        }
    }

    private function seedAuditLogs($students, $counselors): void
    {
        if ($counselors->isEmpty() || $students->isEmpty()) return;

        $actions = [
            ['login',  'User logged in: %s'],
            ['viewed', 'Viewed student profile: %s'],
            ['export', 'Exported analytics: students_2026.csv'],
            ['logout', 'User logged out: %s'],
        ];

        // Generate 30 sample audit entries spread over past 14 days
        for ($i = 0; $i < 30; $i++) {
            $user = $counselors->random();
            $action = ['login','viewed','export','logout','login','viewed','viewed'][rand(0,6)];
            $createdAt = Carbon::now()->subDays(rand(0, 14))->setTime(rand(7, 19), rand(0, 59));

            $description = match ($action) {
                'login'  => "User logged in: {$user->email}",
                'logout' => "User logged out: {$user->email}",
                'viewed' => 'Viewed student profile: ' . $students->random()->full_name,
                'export' => 'Exported analytics: ' . collect(['students','referrals','appointments'])->random() . '_2026.csv',
            };

            $subjectStudent = $action === 'viewed' ? $students->random() : null;

            AuditLog::create([
                'user_id'        => $user->id,
                'action'         => $action,
                'auditable_type' => $subjectStudent ? StudentProfile::class : ($action === 'login' || $action === 'logout' ? User::class : null),
                'auditable_id'   => $subjectStudent?->id ?? (in_array($action, ['login','logout']) ? $user->id : null),
                'description'    => $description,
                'ip_address'     => '192.168.1.' . rand(10, 250),
                'user_agent'     => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at'     => $createdAt,
                'updated_at'     => $createdAt,
            ]);
        }

        // A few failed logins
        for ($i = 0; $i < 3; $i++) {
            AuditLog::create([
                'user_id'     => null,
                'action'      => 'failed_login',
                'description' => 'Failed login attempt for: ' . collect(['unknown@chmsu.edu.ph','test@example.com','wrong.user@chmsu.edu.ph'])->random(),
                'ip_address'  => '192.168.1.' . rand(10, 250),
                'user_agent'  => 'Mozilla/5.0',
                'created_at'  => Carbon::now()->subDays(rand(0, 14)),
                'updated_at'  => Carbon::now()->subDays(rand(0, 14)),
            ]);
        }
    }
}
