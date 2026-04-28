<?php

namespace Database\Seeders;

use App\Models\ActionPlan;
use App\Models\ActionPlanMilestone;
use App\Models\CounselingSession;
use App\Models\Resource;
use App\Models\SessionFeedback;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CounselingFeaturesSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedResources();
        $this->seedActionPlans();
        $this->seedSessionFeedback();

        $this->command?->info('Counseling features sample data seeded.');
    }

    private function seedResources(): void
    {
        $author = User::where('role', 'guidance_director')->first()
              ?? User::where('role', 'guidance_counselor')->first();
        if (!$author) return;

        // PH Crisis Hotlines (real public numbers)
        $hotlines = [
            [
                'title'           => 'National Center for Mental Health (NCMH)',
                'description'     => 'The Department of Health\'s 24/7 crisis hotline for mental health emergencies, suicide prevention, and psychological first aid. Free and confidential.',
                'type'            => 'hotline',
                'category'        => 'crisis',
                'contact_number'  => '1553 (toll-free landline) or 0917-899-USAP (8727)',
                'available_hours' => '24 hours, 7 days a week',
                'is_emergency'    => true,
                'sort_order'      => 1,
            ],
            [
                'title'           => 'In Touch Community Services Crisis Line',
                'description'     => 'Confidential crisis intervention and emotional support for those in distress.',
                'type'            => 'hotline',
                'category'        => 'crisis',
                'contact_number'  => '(02) 8893-7603',
                'available_hours' => '24 hours, 7 days a week',
                'is_emergency'    => true,
                'sort_order'      => 2,
            ],
            [
                'title'           => 'Hopeline Philippines',
                'description'     => 'Suicide prevention and emotional crisis hotline. Trained responders ready to listen.',
                'type'            => 'hotline',
                'category'        => 'crisis',
                'contact_number'  => '0917-558-4673 / (02) 8804-HOPE (4673)',
                'available_hours' => '24/7',
                'is_emergency'    => true,
                'sort_order'      => 3,
            ],
            [
                'title'           => 'CHMSU Guidance Office',
                'description'     => 'Walk-in or call to schedule a counseling session. Confidential support for academic, personal, social, and career concerns.',
                'type'            => 'contact',
                'category'        => 'crisis',
                'contact_number'  => '(034) 460-0511',
                'available_hours' => 'Mon–Fri, 8:00 AM – 5:00 PM',
                'is_emergency'    => false,
                'sort_order'      => 4,
            ],
            [
                'title'           => 'PNP Women & Children Protection Center',
                'description'     => 'For students experiencing domestic violence, abuse, or harassment. Free legal and psychological assistance.',
                'type'            => 'hotline',
                'category'        => 'crisis',
                'contact_number'  => '(02) 8723-0401 local 5260',
                'available_hours' => '24/7',
                'is_emergency'    => true,
                'sort_order'      => 5,
            ],
        ];

        // Mental Health & Self-Care articles/links
        $articles = [
            [
                'title'       => 'Understanding Anxiety: A Student\'s Guide',
                'description' => 'Practical strategies to identify and manage anxiety symptoms during the school year. Learn breathing techniques, grounding exercises, and when to seek help.',
                'type'        => 'article',
                'category'    => 'mental_health',
                'url'         => 'https://www.who.int/news-room/fact-sheets/detail/anxiety-disorders',
                'sort_order'  => 10,
            ],
            [
                'title'       => 'Coping with Academic Stress',
                'description' => 'Tips and techniques to manage exam pressure, deadlines, and academic burnout. Includes a stress management self-assessment.',
                'type'        => 'article',
                'category'    => 'academic',
                'url'         => 'https://www.who.int/news-room/feature-stories/mental-well-being-resources-for-the-public',
                'sort_order'  => 20,
            ],
            [
                'title'       => 'How to Improve Your Sleep',
                'description' => 'Sleep is essential to mental health. Learn evidence-based sleep hygiene practices for college students.',
                'type'        => 'article',
                'category'    => 'self_care',
                'url'         => 'https://www.cdc.gov/sleep/about_sleep/sleep_hygiene.html',
                'sort_order'  => 30,
            ],
            [
                'title'       => 'Mindfulness Meditation for Beginners (Video)',
                'description' => '10-minute guided meditation video to help reduce stress and improve focus.',
                'type'        => 'video',
                'category'    => 'self_care',
                'url'         => 'https://www.youtube.com/watch?v=ZToicYcHIOU',
                'sort_order'  => 40,
            ],
            [
                'title'       => 'Building Healthy Relationships in College',
                'description' => 'Whether romantic, platonic, or with family — learn the foundations of healthy communication and boundaries.',
                'type'        => 'article',
                'category'    => 'relationships',
                'url'         => 'https://www.loveisrespect.org/resources/healthy-relationships/',
                'sort_order'  => 50,
            ],
            [
                'title'       => 'Career Planning Worksheet (PDF)',
                'description' => 'A self-assessment tool to help you explore your interests, skills, and values and align them with potential career paths.',
                'type'        => 'link',
                'category'    => 'career',
                'url'         => 'https://www.dol.gov/agencies/odep/program-areas/individuals/youth/transition/guideposts',
                'sort_order'  => 60,
            ],
            [
                'title'       => 'CHED Scholarship Programs',
                'description' => 'Information on government-funded scholarships available to qualified Filipino students at state universities.',
                'type'        => 'link',
                'category'    => 'financial',
                'url'         => 'https://ched.gov.ph/scholarships/',
                'sort_order'  => 70,
            ],
            [
                'title'       => 'Recognizing Signs of Depression',
                'description' => 'Learn the warning signs of depression in yourself or someone you care about, and how to seek help.',
                'type'        => 'article',
                'category'    => 'mental_health',
                'url'         => 'https://www.nimh.nih.gov/health/topics/depression',
                'sort_order'  => 80,
            ],
        ];

        foreach (array_merge($hotlines, $articles) as $item) {
            Resource::create(array_merge($item, [
                'created_by'   => $author->id,
                'is_published' => true,
                'view_count'   => rand(0, 250),
            ]));
        }
    }

    private function seedActionPlans(): void
    {
        $counselors = User::where('role', 'guidance_counselor')->where('is_active', true)->get();
        $students = StudentProfile::inRandomOrder()->take(6)->get();
        if ($counselors->isEmpty() || $students->isEmpty()) return;

        $templates = [
            [
                'title'       => 'Improve Academic Performance in Major Subjects',
                'description' => "Goal: Raise GPA to 2.5 or higher by end of semester.\n\nThe student is struggling with time management and study skills. We will work together on developing a study schedule and improving comprehension techniques.",
                'focus_area'  => 'academic',
                'milestones'  => [
                    ['Create weekly study schedule', 7],
                    ['Meet with subject teachers for support', 14],
                    ['Complete study skills workshop', 21],
                    ['First grade check-in (mid-semester)', 45],
                    ['Final grade review', 90],
                ],
            ],
            [
                'title'       => 'Manage Anxiety & Build Coping Skills',
                'description' => "Goal: Develop healthy coping mechanisms for academic and social anxiety.\n\nStudent expresses overwhelming anxiety especially before exams and presentations. We'll work on grounding techniques, breathing exercises, and gradual exposure.",
                'focus_area'  => 'mental_health',
                'milestones'  => [
                    ['Learn 4-7-8 breathing technique', 7],
                    ['Daily journaling for 14 days', 14],
                    ['Practice grounding (5-4-3-2-1)', 14],
                    ['Attempt one small presentation', 30],
                    ['Reassessment session', 60],
                ],
            ],
            [
                'title'       => 'Career Exploration Path',
                'description' => "Goal: Identify 3 viable career paths and build a development plan.\n\nStudent is uncertain about post-graduation plans. We'll do interest inventories, informational interviews, and exposure visits.",
                'focus_area'  => 'career',
                'milestones'  => [
                    ['Complete career interest inventory', 7],
                    ['Research 5 potential careers', 21],
                    ['Schedule 2 informational interviews', 45],
                    ['Visit 1 workplace / immersion', 60],
                    ['Finalize top 3 career options', 90],
                ],
            ],
            [
                'title'       => 'Improve Class Attendance',
                'description' => "Goal: Achieve 90% attendance for the remainder of the semester.\n\nStudent has missed >15% of classes due to a mix of personal and motivation issues. We'll address root causes and create accountability.",
                'focus_area'  => 'behavioral',
                'milestones'  => [
                    ['Identify and discuss root causes', 7],
                    ['Set up morning routine plan', 14],
                    ['Weekly attendance check-in (4x)', 35],
                    ['Mid-progress review', 60],
                ],
            ],
        ];

        foreach ($templates as $i => $template) {
            $student = $students[$i % $students->count()];
            $counselor = $counselors->random();
            $startDate = Carbon::now()->subDays(rand(7, 60));

            $plan = ActionPlan::create([
                'student_profile_id' => $student->id,
                'counselor_id'       => $counselor->id,
                'title'              => $template['title'],
                'description'        => $template['description'],
                'focus_area'         => $template['focus_area'],
                'status'             => collect(['active','active','active','completed'])->random(),
                'start_date'         => $startDate,
                'target_date'        => $startDate->copy()->addDays(90),
            ]);

            foreach ($template['milestones'] as $idx => [$desc, $offset]) {
                $targetDate = $startDate->copy()->addDays($offset);
                $completed = $targetDate->isPast() && rand(1, 100) <= 60;

                ActionPlanMilestone::create([
                    'action_plan_id' => $plan->id,
                    'description'    => $desc,
                    'target_date'    => $targetDate,
                    'completed_at'   => $completed ? $targetDate->copy()->addDays(rand(-3, 5)) : null,
                    'sort_order'     => $idx,
                ]);
            }
        }
    }

    private function seedSessionFeedback(): void
    {
        $sessions = CounselingSession::with('studentProfile')->get();
        if ($sessions->isEmpty()) return;

        $worked = [
            'Counselor really listened without judging me.',
            'I felt safe to share what I was going through.',
            'The breathing techniques we practiced really helped.',
            'I appreciated the practical advice I could actually use.',
            'It was helpful to have someone outside my friend group to talk to.',
            null, null,
        ];

        $improve = [
            'Maybe a quieter room — there was noise from outside.',
            'I wish sessions could be a bit longer.',
            'Could use more follow-up resources to take home.',
            'Schedule was hard — would prefer afternoon slots.',
            null, null, null, null,
        ];

        // ~70% of sessions have feedback
        foreach ($sessions as $session) {
            if (rand(1, 100) > 70) continue;
            if (!$session->studentProfile) continue;

            // Skew positive (real-world bias)
            $rating = collect([3,4,4,4,5,5,5,5])->random();
            $helpful = max(1, min(5, $rating + rand(-1, 1)));
            $listened = max(1, min(5, $rating + rand(-1, 1)));
            $comfort = max(1, min(5, $rating + rand(-1, 1)));

            SessionFeedback::create([
                'counseling_session_id' => $session->id,
                'student_profile_id'    => $session->student_profile_id,
                'overall_rating'        => $rating,
                'helpful_score'         => $helpful,
                'listened_score'        => $listened,
                'comfort_score'         => $comfort,
                'would_recommend'       => $rating >= 3,
                'issue_resolved'        => $rating >= 4,
                'what_worked'           => collect($worked)->random(),
                'what_could_improve'    => collect($improve)->random(),
                'created_at'            => $session->created_at->copy()->addHours(rand(2, 48)),
                'updated_at'            => $session->created_at->copy()->addHours(rand(2, 48)),
            ]);
        }
    }
}
