<?php

namespace Database\Seeders;

use App\Models\AnonymousConcern;
use App\Models\StudentProfile;
use App\Models\User;
use App\Models\WalkInQueue;
use App\Models\Workshop;
use App\Models\WorkshopRsvp;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class OperationsSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedWalkInQueue();
        $this->seedAnonymousConcerns();
        $this->seedWorkshops();

        $this->command?->info('Operations sample data seeded.');
    }

    private function seedWalkInQueue(): void
    {
        $students = StudentProfile::inRandomOrder()->take(8)->get();
        $counselors = User::where('role', 'guidance_counselor')->get();
        if ($students->isEmpty() || $counselors->isEmpty()) return;

        $reasons = [
            'Wants to discuss anxiety about upcoming exams',
            'Needs help with class schedule conflicts',
            'Crisis — feeling overwhelmed and tearful',
            'Career guidance — uncertain about major',
            'Family issues affecting attendance',
            'Follow-up on previous session',
            'Requesting Good Moral Certificate',
            'Roommate conflict needs mediation',
        ];

        $now = Carbon::now();
        // Currently in queue (today)
        $queueData = [
            // Waiting
            [$students[0], 'normal',  $reasons[0], 'waiting',    $now->copy()->subMinutes(35)],
            [$students[1], 'urgent',  $reasons[2], 'waiting',    $now->copy()->subMinutes(20)],
            [$students[2], 'crisis',  'Student appeared distressed and needs immediate attention', 'waiting', $now->copy()->subMinutes(5)],
            [$students[3], 'normal',  $reasons[6], 'waiting',    $now->copy()->subMinutes(10)],
            // Being seen
            [$students[4], 'normal',  $reasons[3], 'being_seen', $now->copy()->subMinutes(45), $now->copy()->subMinutes(20)],
            // Completed today
            [$students[5], 'normal',  $reasons[5], 'completed',  $now->copy()->subHours(2),    $now->copy()->subHours(2)->addMinutes(15), $now->copy()->subHours(1)->subMinutes(30)],
            [$students[6], 'urgent',  $reasons[4], 'completed',  $now->copy()->subHours(3),    $now->copy()->subHours(3)->addMinutes(5),  $now->copy()->subHours(2)->subMinutes(20)],
            [$students[7], 'normal',  $reasons[7], 'no_show',    $now->copy()->subHours(4),    null,                                      $now->copy()->subHours(3)->subMinutes(30)],
        ];

        foreach ($queueData as $row) {
            [$student, $priority, $reason, $status, $arrived] = $row;
            $called    = $row[5] ?? null;
            $completed = $row[6] ?? null;

            WalkInQueue::create([
                'student_profile_id'    => $student->id,
                'reason'                => $reason,
                'priority'              => $priority,
                'status'                => $status,
                'assigned_counselor_id' => $status !== 'waiting' ? $counselors->random()->id : null,
                'arrived_at'            => $arrived,
                'called_at'             => $called,
                'completed_at'          => $completed,
                'created_at'            => $arrived,
                'updated_at'            => $completed ?? $called ?? $arrived,
            ]);
        }
    }

    private function seedAnonymousConcerns(): void
    {
        $counselors = User::where('role', 'guidance_counselor')->get();

        $samples = [
            [
                'concern_type' => 'mental_health',
                'urgency'      => 'high',
                'description'  => 'I\'m worried about a classmate. They\'ve stopped showing up to class for over 2 weeks now and when I last saw them they looked exhausted and barely spoke. They posted something on social media that sounded really hopeless. I don\'t want to invade their privacy but I\'m scared something is wrong.',
                'about_who'    => 'Classmate from BS Education, year 3',
                'reporter_relationship' => 'student',
                'status'       => 'reviewing',
                'days_ago'     => 2,
            ],
            [
                'concern_type' => 'bullying',
                'urgency'      => 'medium',
                'description'  => 'There\'s a group of students who consistently make fun of one freshman during PE class. They take photos and post them in their group chats with mean captions. The freshman has started avoiding class.',
                'about_who'    => 'Freshman in BS Computer Science section A',
                'location'     => 'PE classes / Gymnasium',
                'reporter_relationship' => 'student',
                'status'       => 'action_taken',
                'days_ago'     => 7,
            ],
            [
                'concern_type' => 'self_harm',
                'urgency'      => 'critical',
                'description'  => 'I overheard two students in the bathroom talking about cutting. One of them showed the other their wrist and said it helps her cope with the stress at home. I can\'t identify them but it was around the CCS building 3rd floor restroom this morning.',
                'location'     => 'CCS Building 3rd floor',
                'reporter_relationship' => 'student',
                'status'       => 'new',
                'days_ago'     => 0,
            ],
            [
                'concern_type' => 'academic_dishonesty',
                'urgency'      => 'low',
                'description'  => 'Several students openly share answers during exams in one of my classes. The professor doesn\'t seem to notice. It feels unfair to those of us who actually study.',
                'reporter_relationship' => 'student',
                'status'       => 'resolved',
                'days_ago'     => 21,
            ],
            [
                'concern_type' => 'harassment',
                'urgency'      => 'high',
                'description'  => 'A faculty member has been making inappropriate comments about a student\'s appearance during class. Other students have noticed and feel uncomfortable. The student in question seems distressed but afraid to speak up because of grades.',
                'reporter_relationship' => 'student',
                'contact_email' => 'concerned.student@chmsu.edu.ph',
                'status'       => 'reviewing',
                'days_ago'     => 4,
            ],
            [
                'concern_type' => 'safety',
                'urgency'      => 'medium',
                'description'  => 'The lighting near the back parking lot is broken. Students walking to evening classes feel unsafe. There was a near-miss incident last week where a student tripped because she couldn\'t see clearly.',
                'location'     => 'Back parking lot / Evening',
                'reporter_relationship' => 'faculty',
                'status'       => 'action_taken',
                'days_ago'     => 14,
            ],
        ];

        foreach ($samples as $s) {
            $created = Carbon::now()->subDays($s['days_ago']);
            $resolved = $s['status'] === 'resolved' ? $created->copy()->addDays(rand(1, 5)) : null;

            AnonymousConcern::create([
                'reference_code'        => AnonymousConcern::generateReference(),
                'concern_type'          => $s['concern_type'],
                'urgency'               => $s['urgency'],
                'description'           => $s['description'],
                'about_who'             => $s['about_who'] ?? null,
                'location'              => $s['location'] ?? null,
                'reporter_relationship' => $s['reporter_relationship'] ?? null,
                'contact_email'         => $s['contact_email'] ?? null,
                'status'                => $s['status'],
                'handled_by'            => in_array($s['status'], ['reviewing','action_taken','resolved']) ? $counselors->random()?->id : null,
                'staff_notes'           => $s['status'] !== 'new' ? 'Internal notes — case being handled by counseling team.' : null,
                'resolved_at'           => $resolved,
                'ip_address'            => '192.168.1.' . rand(10, 250),
                'created_at'            => $created,
                'updated_at'            => $resolved ?? $created,
            ]);
        }
    }

    private function seedWorkshops(): void
    {
        $organizers = User::whereIn('role', ['guidance_director','guidance_counselor'])->get();
        if ($organizers->isEmpty()) return;

        $workshops = [
            [
                'title'    => 'Mental Health Awareness Kickoff',
                'description' => "Join us for the launch of CHMSU's Mental Health Awareness Week!\n\nThis kickoff session will introduce the week's activities, share what services are available to students year-round, and feature a panel discussion with mental health professionals from the Negros Occidental Provincial Hospital.\n\nFree refreshments. All students welcome.",
                'category' => 'mental_health',
                'venue'    => 'CHMSU Gymnasium',
                'mode'     => 'in_person',
                'starts_in_days' => 5,
                'duration_hours' => 2,
                'capacity' => 200,
                'cover_color' => 'purple',
            ],
            [
                'title'    => 'Stress Management & Study Skills',
                'description' => "Practical techniques to manage exam stress and improve study efficiency.\n\nWe'll cover: time blocking, the Pomodoro technique, active recall study methods, and breathing exercises for test anxiety. Bring a notebook!",
                'category' => 'academic',
                'venue'    => 'Guidance Conference Room',
                'mode'     => 'in_person',
                'starts_in_days' => 8,
                'duration_hours' => 1.5,
                'capacity' => 30,
                'cover_color' => 'blue',
            ],
            [
                'title'    => 'Career Path Workshop: BSIT Graduates',
                'description' => "Featuring guest speakers from local IT companies and CHMSU alumni who'll share their career journeys.\n\nLearn about job-hunting in the IT sector, what employers look for, and how to build your portfolio. Q&A session at the end.",
                'category' => 'career',
                'venue'    => 'CCS Audio-Visual Room',
                'mode'     => 'hybrid',
                'meeting_link' => 'https://meet.google.com/sample-link',
                'starts_in_days' => 12,
                'duration_hours' => 3,
                'capacity' => 80,
                'cover_color' => 'green',
            ],
            [
                'title'    => 'Self-Care for Working Students',
                'description' => "Balancing work and study can feel impossible. This session offers practical self-care strategies tailored to working students at CHMSU.\n\nWe'll discuss boundary-setting, sleep hygiene, micro-breaks, and how to ask for help. Includes a take-home self-care plan template.",
                'category' => 'wellness',
                'venue'    => 'Online (Zoom)',
                'mode'     => 'virtual',
                'meeting_link' => 'https://zoom.us/j/sample',
                'starts_in_days' => 18,
                'duration_hours' => 1,
                'capacity' => 100,
                'cover_color' => 'teal',
            ],
            [
                'title'    => 'Healthy Relationships 101',
                'description' => "Whether romantic, friendship, or family — every relationship benefits from clear communication and healthy boundaries.\n\nThis session covers: identifying red flags, communicating needs, conflict resolution, and recognizing toxic patterns.",
                'category' => 'life_skills',
                'venue'    => 'Library Reading Hall',
                'mode'     => 'in_person',
                'starts_in_days' => 25,
                'duration_hours' => 2,
                'capacity' => 50,
                'cover_color' => 'rose',
            ],
            // Past workshop
            [
                'title'    => 'Financial Literacy for Students',
                'description' => "Last semester's hit workshop on managing your allowance, smart spending, and basic saving for college students.",
                'category' => 'life_skills',
                'venue'    => 'CBA Conference Hall',
                'mode'     => 'in_person',
                'starts_in_days' => -45,
                'duration_hours' => 2,
                'capacity' => 60,
                'cover_color' => 'orange',
                'status' => 'completed',
            ],
        ];

        $students = User::where('role', 'student')->where('is_active', true)->get();

        foreach ($workshops as $w) {
            $startsAt = Carbon::now()->addDays($w['starts_in_days'])->setTime(rand(9, 15), [0, 30][rand(0, 1)]);
            $endsAt   = $startsAt->copy()->addMinutes($w['duration_hours'] * 60);

            $workshop = Workshop::create([
                'organizer_id'  => $organizers->random()->id,
                'title'         => $w['title'],
                'description'   => $w['description'],
                'category'      => $w['category'],
                'venue'         => $w['venue'],
                'mode'          => $w['mode'],
                'meeting_link'  => $w['meeting_link'] ?? null,
                'starts_at'     => $startsAt,
                'ends_at'       => $endsAt,
                'capacity'      => $w['capacity'] ?? null,
                'rsvp_deadline' => $startsAt->copy()->subDay(),
                'audience'      => 'all',
                'status'        => $w['status'] ?? 'published',
                'cover_color'   => $w['cover_color'] ?? 'blue',
            ]);

            // Random RSVPs (30-70% capacity)
            if ($students->isNotEmpty()) {
                $rsvpCount = (int) min($students->count(), ($w['capacity'] ?? 30) * (rand(30, 70) / 100));
                $isPast = $w['starts_in_days'] < 0;

                foreach ($students->random(min($rsvpCount, $students->count())) as $student) {
                    WorkshopRsvp::create([
                        'workshop_id' => $workshop->id,
                        'user_id'     => $student->id,
                        'status'      => $isPast ? (rand(1, 100) <= 75 ? 'attended' : 'no_show') : 'registered',
                        'attended_at' => $isPast && rand(1, 100) <= 75 ? $startsAt->copy()->addMinutes(rand(0, 15)) : null,
                    ]);
                }
            }
        }
    }
}
