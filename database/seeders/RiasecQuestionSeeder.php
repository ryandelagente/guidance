<?php

namespace Database\Seeders;

use App\Models\RiasecQuestion;
use Illuminate\Database\Seeder;

class RiasecQuestionSeeder extends Seeder
{
    public function run(): void
    {
        // 10 questions per RIASEC type — standard inventory
        $bank = [
            'R' => [
                'I like working with my hands to build or repair things.',
                'I enjoy outdoor work like gardening or farming.',
                'I would rather operate machinery than work in an office.',
                'I find working with tools and equipment satisfying.',
                'I prefer physical activity over sitting at a desk.',
                'I like fixing electrical or mechanical things.',
                'I enjoy sports, hiking, or other physical hobbies.',
                'I would rather build a house than design one on paper.',
                'I like working with animals.',
                'I enjoy hands-on technical work like welding or carpentry.',
            ],
            'I' => [
                'I like solving puzzles and complex problems.',
                'I enjoy doing scientific experiments.',
                'I read books or articles about science or technology for fun.',
                'I am curious about how things work.',
                'I like analyzing data and figuring out patterns.',
                'I enjoy logical, mathematical reasoning.',
                'I like researching topics in depth.',
                'I prefer thinking and theorizing over taking action.',
                'I enjoy working independently on intellectual problems.',
                'I would rather understand a concept than memorize it.',
            ],
            'A' => [
                'I enjoy drawing, painting, or other visual arts.',
                'I like writing stories, poems, or essays.',
                'I enjoy playing or composing music.',
                'I prefer creative tasks over routine ones.',
                'I would like to work in a creative field like design or media.',
                'I enjoy decorating, styling, or arranging things artistically.',
                'I like attending concerts, theater, or art exhibits.',
                'I express myself best through creative work.',
                'I enjoy photography or filmmaking.',
                'I find inspiration in colors, sounds, and aesthetics.',
            ],
            'S' => [
                'I enjoy helping people solve their problems.',
                'I like teaching or tutoring others.',
                'I enjoy listening to people and understanding their feelings.',
                'I would like to work in healthcare or counseling.',
                'I like volunteering for community service.',
                'I enjoy working in teams more than alone.',
                'People often come to me for advice.',
                'I want to make a positive difference in others\' lives.',
                'I enjoy taking care of children, elderly, or those in need.',
                'I would like to work in education or social work.',
            ],
            'E' => [
                'I like leading group projects or activities.',
                'I enjoy persuading others to see my point of view.',
                'I would enjoy starting and running my own business.',
                'I am comfortable making important decisions.',
                'I like being in charge of teams or events.',
                'I enjoy public speaking or giving presentations.',
                'I am motivated by competition and winning.',
                'I would like a career in business, sales, or politics.',
                'I am confident negotiating with others.',
                'I enjoy meeting new people and networking.',
            ],
            'C' => [
                'I like organizing files, schedules, or information.',
                'I enjoy following clear procedures and instructions.',
                'I prefer detailed, step-by-step work.',
                'I like keeping accurate records or accounts.',
                'I would enjoy working with numbers and budgets.',
                'I prefer a structured, predictable routine.',
                'I am careful and accurate with details.',
                'I enjoy working with computer software like spreadsheets or databases.',
                'I would rather follow a clear plan than improvise.',
                'I like office work that requires precision and order.',
            ],
        ];

        $sortOrder = 0;
        // Interleave types so the test feels varied (R, I, A, S, E, C, R, I, ...)
        for ($i = 0; $i < 10; $i++) {
            foreach (['R','I','A','S','E','C'] as $type) {
                if (!isset($bank[$type][$i])) continue;
                RiasecQuestion::firstOrCreate(
                    ['text' => $bank[$type][$i], 'type' => $type],
                    ['sort_order' => $sortOrder++, 'is_active' => true]
                );
            }
        }

        $this->command?->info('RIASEC question bank seeded (60 questions).');
    }
}
