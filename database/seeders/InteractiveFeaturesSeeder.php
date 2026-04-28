<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\RiasecQuestion;
use App\Models\RiasecResponse;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class InteractiveFeaturesSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RiasecQuestionSeeder::class);
        $this->seedRiasecResponses();
        $this->seedConversations();

        $this->command?->info('Interactive features seeded.');
    }

    private function seedRiasecResponses(): void
    {
        $students = StudentProfile::inRandomOrder()->take(5)->get();
        $questions = RiasecQuestion::all()->groupBy('type');

        if ($questions->isEmpty()) return;

        // Different personality archetypes
        $archetypes = [
            // Strong Social/Artistic
            ['S' => 9, 'A' => 7, 'I' => 5, 'E' => 4, 'R' => 2, 'C' => 3],
            // Investigative/Realistic (typical engineer/scientist)
            ['I' => 9, 'R' => 8, 'C' => 5, 'A' => 3, 'S' => 4, 'E' => 3],
            // Enterprising/Conventional (business)
            ['E' => 9, 'C' => 8, 'S' => 5, 'I' => 4, 'A' => 3, 'R' => 2],
            // Artistic/Social (creative helper)
            ['A' => 10, 'S' => 8, 'I' => 5, 'E' => 4, 'C' => 2, 'R' => 3],
            // Realistic/Conventional (technical/structured)
            ['R' => 8, 'C' => 8, 'I' => 6, 'E' => 4, 'S' => 3, 'A' => 2],
        ];

        foreach ($students as $i => $student) {
            $scores = $archetypes[$i % count($archetypes)];
            arsort($scores);
            $topCode = implode('', array_slice(array_keys($scores), 0, 3));

            // Generate plausible answers
            $answers = [];
            foreach ($questions as $type => $bank) {
                $targetCount = $scores[$type] ?? 0;
                $idsLikedExpected = $bank->shuffle()->take($targetCount)->pluck('id')->toArray();
                foreach ($bank as $q) {
                    $answers[$q->id] = in_array($q->id, $idsLikedExpected) ? 1 : 0;
                }
            }

            RiasecResponse::create([
                'student_profile_id' => $student->id,
                'score_r' => $scores['R'] ?? 0,
                'score_i' => $scores['I'] ?? 0,
                'score_a' => $scores['A'] ?? 0,
                'score_s' => $scores['S'] ?? 0,
                'score_e' => $scores['E'] ?? 0,
                'score_c' => $scores['C'] ?? 0,
                'top_code'     => $topCode,
                'answers'      => $answers,
                'completed_at' => Carbon::now()->subDays(rand(1, 60)),
            ]);
        }
    }

    private function seedConversations(): void
    {
        $counselors = User::where('role', 'guidance_counselor')->where('is_active', true)->get();
        $studentUsers = User::where('role', 'student')->where('is_active', true)->take(6)->get();

        if ($counselors->isEmpty() || $studentUsers->isEmpty()) return;

        $conversationStarters = [
            [
                'subject' => 'Following up on our session',
                'thread' => [
                    ['c', 'Hi! Just wanted to check in after our session yesterday. How are you feeling about the strategies we discussed?'],
                    ['s', 'Hi po! I tried the breathing exercise during my exam this morning. It helped a bit, salamat po!'],
                    ['c', 'That\'s wonderful to hear! It\'s normal for it to take some practice. Want to schedule a follow-up next week?'],
                    ['s', 'Yes po, that would help. What time works for you?'],
                ],
            ],
            [
                'subject' => 'Action plan check-in',
                'thread' => [
                    ['c', 'Hi! How is your action plan going? I noticed you completed the first milestone — congrats!'],
                    ['s', 'Hi po! Yes, I finished it last week. Working on the next one now but it\'s harder than I thought.'],
                    ['c', 'Totally normal — what part is feeling difficult? We can adjust the plan if needed.'],
                ],
            ],
            [
                'subject' => 'Career test results',
                'thread' => [
                    ['c', 'Hi, I noticed you finished your RIASEC test. Would you like to schedule a meeting to talk through your results and what they could mean for your career path?'],
                    ['s', 'Yes po that would be great! I\'m a bit confused about what some of the careers actually mean.'],
                    ['c', 'No worries, that\'s exactly what we\'ll explore together. Are you free Thursday afternoon?'],
                ],
            ],
            [
                'subject' => null,
                'thread' => [
                    ['c', 'Hello! I just wanted to reach out and let you know I\'m here if you need to talk. Take your time, no pressure.'],
                ],
            ],
            [
                'subject' => 'Quick reminder',
                'thread' => [
                    ['c', 'Hi! Just a friendly reminder you have an appointment with me tomorrow at 2 PM. See you then!'],
                    ['s', 'Got it po, thank you!'],
                ],
            ],
            [
                'subject' => 'Mental Health Awareness Week',
                'thread' => [
                    ['c', 'Hi! Mental Health Awareness Week starts next Monday. There\'s a stress management workshop on Tuesday I think you\'d enjoy. Want me to RSVP for you?'],
                ],
            ],
        ];

        foreach ($conversationStarters as $i => $convo) {
            if (!isset($studentUsers[$i])) continue;
            $counselor = $counselors->random();
            $student = $studentUsers[$i];

            $created = Carbon::now()->subDays(rand(0, 14));

            $conversation = Conversation::create([
                'counselor_id'    => $counselor->id,
                'student_user_id' => $student->id,
                'subject'         => $convo['subject'],
                'last_message_at' => $created,
                'created_at'      => $created,
                'updated_at'      => $created,
            ]);

            $msgTime = $created->copy();
            $totalMessages = count($convo['thread']);

            foreach ($convo['thread'] as $idx => [$role, $body]) {
                $msgTime = $msgTime->copy()->addMinutes(rand(15, 360));
                $sender = $role === 'c' ? $counselor : $student;
                $isLast = ($idx === $totalMessages - 1);
                // The last message from the OTHER party stays unread for some convos
                $readByOther = !$isLast || (rand(1, 100) <= 60);

                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id'       => $sender->id,
                    'body'            => $body,
                    'read_at'         => $readByOther ? $msgTime->copy()->addMinutes(rand(5, 240)) : null,
                    'created_at'      => $msgTime,
                    'updated_at'      => $msgTime,
                ]);
            }

            $conversation->update(['last_message_at' => $msgTime]);
        }
    }
}
