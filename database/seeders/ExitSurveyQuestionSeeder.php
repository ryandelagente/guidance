<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExitSurveyQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            [
                'question_text' => 'How would you rate the overall quality of the guidance and counseling services you received?',
                'question_type' => 'rating_1_5',
                'options'       => null,
                'is_required'   => true,
                'sort_order'    => 1,
            ],
            [
                'question_text' => 'How helpful were the counseling sessions in addressing your personal or academic concerns?',
                'question_type' => 'rating_1_5',
                'options'       => null,
                'is_required'   => true,
                'sort_order'    => 2,
            ],
            [
                'question_text' => 'Were the guidance counselors approachable and professional?',
                'question_type' => 'yes_no',
                'options'       => null,
                'is_required'   => true,
                'sort_order'    => 3,
            ],
            [
                'question_text' => 'Which service did you find most beneficial during your stay at CHMSU?',
                'question_type' => 'multiple_choice',
                'options'       => json_encode(['Counseling Sessions', 'Career Guidance', 'Psychological Testing', 'Referral Assistance', 'Clearance Processing']),
                'is_required'   => true,
                'sort_order'    => 4,
            ],
            [
                'question_text' => 'What challenges did you encounter in accessing guidance services? Please describe briefly.',
                'question_type' => 'text',
                'options'       => null,
                'is_required'   => false,
                'sort_order'    => 5,
            ],
            [
                'question_text' => 'Did the guidance office adequately support your mental health and well-being?',
                'question_type' => 'yes_no',
                'options'       => null,
                'is_required'   => true,
                'sort_order'    => 6,
            ],
            [
                'question_text' => 'What suggestions do you have to improve the Guidance and Counseling Office services for future students?',
                'question_type' => 'text',
                'options'       => null,
                'is_required'   => false,
                'sort_order'    => 7,
            ],
            [
                'question_text' => 'How do you rate the timeliness of the guidance office in responding to your needs?',
                'question_type' => 'rating_1_5',
                'options'       => null,
                'is_required'   => true,
                'sort_order'    => 8,
            ],
            [
                'question_text' => 'Would you recommend CHMSU\'s Guidance and Counseling services to incoming students?',
                'question_type' => 'yes_no',
                'options'       => null,
                'is_required'   => true,
                'sort_order'    => 9,
            ],
            [
                'question_text' => 'Any final message or feedback for the CHMSU Guidance Office?',
                'question_type' => 'text',
                'options'       => null,
                'is_required'   => false,
                'sort_order'    => 10,
            ],
        ];

        foreach ($questions as $q) {
            DB::table('exit_survey_questions')->insertOrIgnore(array_merge($q, [
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
