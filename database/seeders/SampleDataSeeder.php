<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\ClearanceRequest;
use App\Models\CounselingSession;
use App\Models\CounselorSchedule;
use App\Models\DisciplinaryRecord;
use App\Models\EmergencyContact;
use App\Models\GoodMoralCertificate;
use App\Models\PsychologicalTest;
use App\Models\Referral;
use App\Models\ReferralIntervention;
use App\Models\StudentProfile;
use App\Models\TestResult;
use App\Models\TestSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Users ─────────────────────────────────────────────────────────────

        $director = User::firstOrCreate(['email' => 'director@chmsu.edu.ph'], [
            'name'      => 'Dr. Maria Santos',
            'password'  => Hash::make('Password@123'),
            'role'      => 'guidance_director',
            'is_active' => true,
        ]);

        $counselor1 = User::firstOrCreate(['email' => 'counselor1@chmsu.edu.ph'], [
            'name'      => 'Ms. Ana Reyes',
            'password'  => Hash::make('Password@123'),
            'role'      => 'guidance_counselor',
            'is_active' => true,
        ]);

        $counselor2 = User::firstOrCreate(['email' => 'counselor2@chmsu.edu.ph'], [
            'name'      => 'Mr. Jose Dela Cruz',
            'password'  => Hash::make('Password@123'),
            'role'      => 'guidance_counselor',
            'is_active' => true,
        ]);

        $faculty1 = User::firstOrCreate(['email' => 'faculty1@chmsu.edu.ph'], [
            'name'      => 'Prof. Roberto Lim',
            'password'  => Hash::make('Password@123'),
            'role'      => 'faculty',
            'is_active' => true,
        ]);

        $faculty2 = User::firstOrCreate(['email' => 'faculty2@chmsu.edu.ph'], [
            'name'      => 'Prof. Grace Villanueva',
            'password'  => Hash::make('Password@123'),
            'role'      => 'faculty',
            'is_active' => true,
        ]);

        // Student users
        $studentUsers = [
            ['email' => 'student1@chmsu.edu.ph', 'name' => 'Juan dela Cruz'],
            ['email' => 'student2@chmsu.edu.ph', 'name' => 'Maria Flores'],
            ['email' => 'student3@chmsu.edu.ph', 'name' => 'Carlo Santos'],
            ['email' => 'student4@chmsu.edu.ph', 'name' => 'Liza Reyes'],
            ['email' => 'student5@chmsu.edu.ph', 'name' => 'Miguel Torres'],
            ['email' => 'student6@chmsu.edu.ph', 'name' => 'Anna Garcia'],
            ['email' => 'student7@chmsu.edu.ph', 'name' => 'Rico Mendoza'],
            ['email' => 'student8@chmsu.edu.ph', 'name' => 'Sophia Bautista'],
        ];

        $students = [];
        foreach ($studentUsers as $su) {
            $students[] = User::firstOrCreate(['email' => $su['email']], [
                'name'      => $su['name'],
                'password'  => Hash::make('Password@123'),
                'role'      => 'student',
                'is_active' => true,
            ]);
        }

        // ── 2. Student Profiles ───────────────────────────────────────────────────

        $profileData = [
            [
                'user_id' => $students[0]->id, 'first_name' => 'Juan', 'last_name' => 'dela Cruz',
                'middle_name' => 'Reyes', 'date_of_birth' => '2003-06-15', 'sex' => 'male',
                'civil_status' => 'single', 'religion' => 'Roman Catholic', 'nationality' => 'Filipino',
                'contact_number' => '09171234501', 'home_address' => 'Brgy. 1, Victorias City, Negros Occidental',
                'student_id_number' => '2022-IT-001', 'college' => 'College of Engineering and Technology',
                'program' => 'BSIT', 'year_level' => '3rd Year', 'student_type' => 'regular',
                'academic_status' => 'good_standing', 'father_name' => 'Pedro dela Cruz',
                'mother_name' => 'Rosa dela Cruz', 'assigned_counselor_id' => $counselor1->id,
            ],
            [
                'user_id' => $students[1]->id, 'first_name' => 'Maria', 'last_name' => 'Flores',
                'middle_name' => 'Santos', 'date_of_birth' => '2002-03-22', 'sex' => 'female',
                'civil_status' => 'single', 'religion' => 'Roman Catholic', 'nationality' => 'Filipino',
                'contact_number' => '09181234502', 'home_address' => 'Brgy. 5, Talisay City, Negros Occidental',
                'student_id_number' => '2021-NUR-007', 'college' => 'College of Nursing',
                'program' => 'BSN', 'year_level' => '4th Year', 'student_type' => 'regular',
                'academic_status' => 'good_standing', 'father_name' => 'Ricardo Flores',
                'mother_name' => 'Josefa Flores', 'assigned_counselor_id' => $counselor1->id,
            ],
            [
                'user_id' => $students[2]->id, 'first_name' => 'Carlo', 'last_name' => 'Santos',
                'middle_name' => 'Marajas', 'date_of_birth' => '2004-11-08', 'sex' => 'male',
                'civil_status' => 'single', 'religion' => 'Iglesia ni Cristo', 'nationality' => 'Filipino',
                'contact_number' => '09271234503', 'home_address' => 'Brgy. San Pablo, Silay City, Negros Occidental',
                'student_id_number' => '2023-BA-003', 'college' => 'College of Business and Management',
                'program' => 'BSBA', 'year_level' => '2nd Year', 'student_type' => 'irregular',
                'academic_status' => 'at_risk', 'father_name' => 'Ernesto Santos',
                'mother_name' => 'Celia Santos', 'assigned_counselor_id' => $counselor2->id,
                'is_working_student' => true,
            ],
            [
                'user_id' => $students[3]->id, 'first_name' => 'Liza', 'last_name' => 'Reyes',
                'middle_name' => 'Cruz', 'date_of_birth' => '2003-07-30', 'sex' => 'female',
                'civil_status' => 'single', 'religion' => 'Roman Catholic', 'nationality' => 'Filipino',
                'contact_number' => '09991234504', 'home_address' => 'Brgy. Zone 6, Bacolod City',
                'student_id_number' => '2022-ED-015', 'college' => 'College of Education',
                'program' => 'BSED', 'year_level' => '3rd Year', 'student_type' => 'regular',
                'academic_status' => 'good_standing', 'father_name' => 'Manuel Reyes',
                'mother_name' => 'Ligaya Reyes', 'assigned_counselor_id' => $counselor1->id,
                'scholarship' => 'CHED Scholarship',
            ],
            [
                'user_id' => $students[4]->id, 'first_name' => 'Miguel', 'last_name' => 'Torres',
                'middle_name' => 'Villanueva', 'date_of_birth' => '2001-09-14', 'sex' => 'male',
                'civil_status' => 'single', 'religion' => 'Roman Catholic', 'nationality' => 'Filipino',
                'contact_number' => '09091234505', 'home_address' => 'Brgy. Alijis, Bacolod City',
                'student_id_number' => '2020-CS-002', 'college' => 'College of Engineering and Technology',
                'program' => 'BSCS', 'year_level' => '4th Year', 'student_type' => 'regular',
                'academic_status' => 'good_standing', 'father_name' => 'Rodrigo Torres',
                'mother_name' => 'Melinda Torres', 'assigned_counselor_id' => $counselor2->id,
            ],
            [
                'user_id' => $students[5]->id, 'first_name' => 'Anna', 'last_name' => 'Garcia',
                'middle_name' => 'Pascual', 'date_of_birth' => '2004-02-18', 'sex' => 'female',
                'civil_status' => 'single', 'religion' => 'Born Again Christian', 'nationality' => 'Filipino',
                'contact_number' => '09151234506', 'home_address' => 'Brgy. Bata, Bacolod City',
                'student_id_number' => '2023-IT-009', 'college' => 'College of Engineering and Technology',
                'program' => 'BSIT', 'year_level' => '2nd Year', 'student_type' => 'regular',
                'academic_status' => 'good_standing', 'father_name' => 'Danilo Garcia',
                'mother_name' => 'Remedios Garcia', 'assigned_counselor_id' => $counselor1->id,
            ],
            [
                'user_id' => $students[6]->id, 'first_name' => 'Rico', 'last_name' => 'Mendoza',
                'middle_name' => 'Balboa', 'date_of_birth' => '2003-05-25', 'sex' => 'male',
                'civil_status' => 'single', 'religion' => 'Roman Catholic', 'nationality' => 'Filipino',
                'contact_number' => '09451234507', 'home_address' => 'Brgy. Mansilingan, Bacolod City',
                'student_id_number' => '2022-COMM-004', 'college' => 'College of Arts and Sciences',
                'program' => 'ABComm', 'year_level' => '3rd Year', 'student_type' => 'regular',
                'academic_status' => 'probation', 'father_name' => 'Armando Mendoza',
                'mother_name' => 'Divina Mendoza', 'assigned_counselor_id' => $counselor2->id,
            ],
            [
                'user_id' => $students[7]->id, 'first_name' => 'Sophia', 'last_name' => 'Bautista',
                'middle_name' => 'Gomez', 'date_of_birth' => '2002-12-05', 'sex' => 'female',
                'civil_status' => 'single', 'religion' => 'Roman Catholic', 'nationality' => 'Filipino',
                'contact_number' => '09771234508', 'home_address' => 'Brgy. Tangub, Bacolod City',
                'student_id_number' => '2021-NUR-011', 'college' => 'College of Nursing',
                'program' => 'BSN', 'year_level' => '4th Year', 'student_type' => 'regular',
                'academic_status' => 'good_standing', 'father_name' => 'Eduardo Bautista',
                'mother_name' => 'Fatima Bautista', 'assigned_counselor_id' => $counselor2->id,
            ],
        ];

        $profiles = [];
        foreach ($profileData as $pd) {
            $profiles[] = StudentProfile::firstOrCreate(
                ['student_id_number' => $pd['student_id_number']],
                $pd
            );
        }

        // ── 3. Emergency Contacts ─────────────────────────────────────────────────

        foreach ($profiles as $i => $profile) {
            EmergencyContact::firstOrCreate(
                ['student_profile_id' => $profile->id, 'relationship' => 'Parent'],
                [
                    'name'           => $profile->father_name ?? 'Parent',
                    'contact_number' => '0917100' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'address'        => $profile->home_address,
                ]
            );
        }

        // ── 4. Counselor Schedules ────────────────────────────────────────────────

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        foreach ([$counselor1, $counselor2] as $counselor) {
            foreach ($days as $day) {
                CounselorSchedule::firstOrCreate(
                    ['counselor_id' => $counselor->id, 'day_of_week' => $day],
                    ['start_time' => '08:00:00', 'end_time' => '17:00:00', 'slot_duration' => 60, 'is_active' => true]
                );
            }
        }

        // ── 5. Psychological Tests ────────────────────────────────────────────────

        $testDefs = [
            [
                'name' => 'Otis-Lennon School Ability Test (OLSAT)',
                'test_type' => 'iq', 'category' => 'group',
                'description' => 'Measures reasoning and problem-solving skills for academic placement.',
                'total_items' => 80, 'publisher' => 'Pearson', 'edition_year' => 2023, 'is_active' => true,
            ],
            [
                'name' => 'Myers-Briggs Type Indicator (MBTI)',
                'test_type' => 'personality', 'category' => 'group',
                'description' => 'Identifies personality type preferences across four dichotomies.',
                'total_items' => 93, 'publisher' => 'CPP Inc.', 'edition_year' => 2022, 'is_active' => true,
            ],
            [
                'name' => 'Holland Occupational Themes (RIASEC)',
                'test_type' => 'career_aptitude', 'category' => 'group',
                'description' => 'Matches student interest patterns to career fields using Holland codes.',
                'total_items' => 60, 'publisher' => 'PAR', 'edition_year' => 2021, 'is_active' => true,
            ],
            [
                'name' => 'Beck Anxiety Inventory (BAI)',
                'test_type' => 'mental_health', 'category' => 'individual',
                'description' => 'Measures the severity of anxiety symptoms in the past week.',
                'total_items' => 21, 'publisher' => 'Pearson', 'edition_year' => 2021, 'is_active' => true,
            ],
            [
                'name' => 'Student Strengths Inventory (SSI)',
                'test_type' => 'interest', 'category' => 'group',
                'description' => 'Identifies student strengths and areas of interest for academic planning.',
                'total_items' => 50, 'publisher' => 'Local', 'edition_year' => 2024, 'is_active' => true,
            ],
        ];

        $tests = [];
        foreach ($testDefs as $td) {
            $tests[] = PsychologicalTest::firstOrCreate(['name' => $td['name']], $td);
        }

        // ── 6. Test Schedules ─────────────────────────────────────────────────────

        $sched1 = TestSchedule::firstOrCreate(
            ['psychological_test_id' => $tests[0]->id, 'scheduled_date' => '2026-03-10'],
            [
                'psychological_test_id'  => $tests[0]->id,
                'scheduled_date'         => '2026-03-10',
                'start_time'             => '08:00:00',
                'venue'                  => 'Room 101, Main Building',
                'administered_by'        => $counselor1->id,
                'expected_participants'  => 120,
                'notes'                  => 'Bring pencil and eraser. No electronic devices.',
                'status'                 => 'completed',
            ]
        );

        $sched2 = TestSchedule::firstOrCreate(
            ['psychological_test_id' => $tests[2]->id, 'scheduled_date' => '2026-04-05'],
            [
                'psychological_test_id'  => $tests[2]->id,
                'scheduled_date'         => '2026-04-05',
                'start_time'             => '13:00:00',
                'venue'                  => 'Guidance Office Conference Room',
                'administered_by'        => $counselor2->id,
                'expected_participants'  => 45,
                'notes'                  => 'For 3rd and 4th year students.',
                'status'                 => 'completed',
            ]
        );

        $sched3 = TestSchedule::firstOrCreate(
            ['psychological_test_id' => $tests[1]->id, 'scheduled_date' => '2026-05-12'],
            [
                'psychological_test_id'  => $tests[1]->id,
                'scheduled_date'         => '2026-05-12',
                'start_time'             => '09:00:00',
                'venue'                  => 'AVR, Main Building',
                'administered_by'        => $counselor1->id,
                'expected_participants'  => 80,
                'status'                 => 'scheduled',
            ]
        );

        // ── 7. Appointments ───────────────────────────────────────────────────────

        $appointmentData = [
            // Past completed
            [
                'student_profile_id' => $profiles[0]->id, 'counselor_id' => $counselor1->id,
                'appointment_type' => 'academic', 'appointment_date' => '2026-03-15',
                'start_time' => '09:00:00', 'end_time' => '10:00:00',
                'status' => 'completed', 'meeting_type' => 'in_person',
                'student_concern' => 'Having difficulty with major subjects and considering shifting programs.',
            ],
            [
                'student_profile_id' => $profiles[1]->id, 'counselor_id' => $counselor1->id,
                'appointment_type' => 'personal_social', 'appointment_date' => '2026-03-20',
                'start_time' => '10:00:00', 'end_time' => '11:00:00',
                'status' => 'completed', 'meeting_type' => 'in_person',
                'student_concern' => 'Feeling overwhelmed with clinical duties and board exam preparation.',
            ],
            [
                'student_profile_id' => $profiles[2]->id, 'counselor_id' => $counselor2->id,
                'appointment_type' => 'academic', 'appointment_date' => '2026-04-02',
                'start_time' => '13:00:00', 'end_time' => '14:00:00',
                'status' => 'completed', 'meeting_type' => 'in_person',
                'student_concern' => 'Struggling to keep up with academics while working part-time.',
            ],
            [
                'student_profile_id' => $profiles[6]->id, 'counselor_id' => $counselor2->id,
                'appointment_type' => 'personal_social', 'appointment_date' => '2026-04-10',
                'start_time' => '14:00:00', 'end_time' => '15:00:00',
                'status' => 'completed', 'meeting_type' => 'in_person',
                'student_concern' => 'Conflict with classmates and increased absences from class.',
            ],
            // Upcoming/confirmed
            [
                'student_profile_id' => $profiles[3]->id, 'counselor_id' => $counselor1->id,
                'appointment_type' => 'career', 'appointment_date' => '2026-04-28',
                'start_time' => '10:00:00', 'end_time' => '11:00:00',
                'status' => 'confirmed', 'meeting_type' => 'in_person',
                'student_concern' => 'Interested in pursuing graduate school after graduation.',
                'notes_for_student' => 'Please bring your academic records for review.',
            ],
            [
                'student_profile_id' => $profiles[4]->id, 'counselor_id' => $counselor2->id,
                'appointment_type' => 'career', 'appointment_date' => '2026-04-29',
                'start_time' => '09:00:00', 'end_time' => '10:00:00',
                'status' => 'confirmed', 'meeting_type' => 'in_person',
                'student_concern' => 'Exploring job opportunities in software development.',
                'notes_for_student' => 'Bring your updated resume for review.',
            ],
            [
                'student_profile_id' => $profiles[5]->id, 'counselor_id' => $counselor1->id,
                'appointment_type' => 'academic', 'appointment_date' => '2026-05-02',
                'start_time' => '13:00:00', 'end_time' => '14:00:00',
                'status' => 'pending', 'meeting_type' => 'in_person',
                'student_concern' => 'Request for academic advising for next semester enrollment.',
            ],
            [
                'student_profile_id' => $profiles[7]->id, 'counselor_id' => $counselor2->id,
                'appointment_type' => 'personal_social', 'appointment_date' => '2026-05-05',
                'start_time' => '15:00:00', 'end_time' => '16:00:00',
                'status' => 'pending', 'meeting_type' => 'virtual',
                'meeting_link' => 'https://meet.google.com/abc-def-ghi',
                'student_concern' => 'Anxiety about upcoming board exam.',
            ],
            // Cancelled
            [
                'student_profile_id' => $profiles[0]->id, 'counselor_id' => $counselor1->id,
                'appointment_type' => 'academic', 'appointment_date' => '2026-04-05',
                'start_time' => '09:00:00', 'end_time' => '10:00:00',
                'status' => 'cancelled', 'meeting_type' => 'in_person',
                'student_concern' => 'Follow-up on program shift inquiry.',
                'cancelled_reason' => 'Student was sick and could not attend.',
            ],
        ];

        $appointments = [];
        foreach ($appointmentData as $ad) {
            $appointments[] = Appointment::firstOrCreate(
                [
                    'student_profile_id' => $ad['student_profile_id'],
                    'appointment_date'   => $ad['appointment_date'],
                    'start_time'         => $ad['start_time'],
                ],
                $ad
            );
        }

        // ── 8. Counseling Sessions (for completed appointments) ───────────────────

        $sessionData = [
            [
                'appointment_id'     => $appointments[0]->id,
                'counselor_id'       => $counselor1->id,
                'student_profile_id' => $profiles[0]->id,
                'session_status'     => 'ongoing',
                'presenting_concern' => 'academic',
                'recommendations'    => 'Review course requirements for BSIT. Student advised to consult with department chair regarding shifting options.',
                'follow_up_date'     => '2026-04-28',
                'case_notes'         => 'Student expressed frustration with programming subjects. Shows interest in design-related fields. GPA currently at 2.1. Discussed option of shifting to BSIT-IS track or exploring transfer to BSBA-MIS.',
            ],
            [
                'appointment_id'     => $appointments[1]->id,
                'counselor_id'       => $counselor1->id,
                'student_profile_id' => $profiles[1]->id,
                'session_status'     => 'terminated',
                'presenting_concern' => 'mental_health',
                'recommendations'    => 'Encouraged to join peer support group. Referred to wellness activities. Follow-up after board exam.',
                'follow_up_date'     => null,
                'case_notes'         => 'Student reported sleep difficulties and persistent worry about NLE performance. Administered BAI screening — mild anxiety range. Provided psychoeducation on anxiety management techniques.',
            ],
            [
                'appointment_id'     => $appointments[2]->id,
                'counselor_id'       => $counselor2->id,
                'student_profile_id' => $profiles[2]->id,
                'session_status'     => 'ongoing',
                'presenting_concern' => 'financial',
                'recommendations'    => 'Referred to scholarship office for financial aid options. Advised to reduce work hours during exam period.',
                'follow_up_date'     => '2026-05-10',
                'case_notes'         => 'Student works 6 hours daily in a nearby store. Missing classes due to work conflicts. Academic performance declining — 3 subjects with grades below 75. Discussed time management strategies.',
            ],
            [
                'appointment_id'     => $appointments[3]->id,
                'counselor_id'       => $counselor2->id,
                'student_profile_id' => $profiles[6]->id,
                'session_status'     => 'ongoing',
                'presenting_concern' => 'personal_social',
                'recommendations'    => 'Encouraged to attend conflict resolution workshop. Absences to be reported to class adviser for monitoring.',
                'follow_up_date'     => '2026-05-12',
                'case_notes'         => 'Student described ongoing verbal conflict with two classmates over group project roles. Has accumulated 6 absences. Expressed feeling singled out. Active listening and assertiveness training discussed.',
            ],
        ];

        foreach ($sessionData as $sd) {
            CounselingSession::firstOrCreate(
                ['appointment_id' => $sd['appointment_id']],
                $sd
            );
        }

        // ── 9. Referrals ──────────────────────────────────────────────────────────

        $referralData = [
            [
                'student_profile_id'  => $profiles[2]->id,
                'referred_by'         => $faculty1->id,
                'assigned_counselor_id' => $counselor2->id,
                'reason_category'     => 'attendance',
                'urgency'             => 'high',
                'description'         => 'Carlo Santos has been absent for 8 consecutive class sessions. He appears fatigued and disengaged when present. Classmates mentioned he has been working night shifts.',
                'status'              => 'in_progress',
                'faculty_feedback'    => 'Guidance Office has acknowledged the referral and is currently working with the student.',
                'acknowledged_at'     => now()->subDays(20),
            ],
            [
                'student_profile_id'  => $profiles[6]->id,
                'referred_by'         => $faculty2->id,
                'assigned_counselor_id' => $counselor2->id,
                'reason_category'     => 'behavioral',
                'urgency'             => 'medium',
                'description'         => 'Rico Mendoza was involved in a verbal altercation during class. He has been observed to be increasingly irritable. Three classmates have separately raised concerns.',
                'status'              => 'in_progress',
                'faculty_feedback'    => 'Student has been contacted. Counseling session conducted.',
                'acknowledged_at'     => now()->subDays(15),
            ],
            [
                'student_profile_id'  => $profiles[0]->id,
                'referred_by'         => $faculty1->id,
                'assigned_counselor_id' => $counselor1->id,
                'reason_category'     => 'academic',
                'urgency'             => 'medium',
                'description'         => 'Juan dela Cruz scored below 60% in two major exams consecutively. He seems disinterested and often asks about shifting programs.',
                'status'              => 'resolved',
                'faculty_feedback'    => 'Student has completed counseling. Academic plan has been set.',
                'acknowledged_at'     => now()->subDays(40),
                'resolved_at'         => now()->subDays(10),
            ],
            [
                'student_profile_id'  => $profiles[4]->id,
                'referred_by'         => $faculty2->id,
                'assigned_counselor_id' => null,
                'reason_category'     => 'mental_health',
                'urgency'             => 'critical',
                'description'         => 'Miguel Torres expressed in class that he has been feeling hopeless and unmotivated. He has isolated himself from peers and skipped group activities.',
                'status'              => 'pending',
            ],
        ];

        $referrals = [];
        foreach ($referralData as $rd) {
            $referrals[] = Referral::firstOrCreate(
                [
                    'student_profile_id' => $rd['student_profile_id'],
                    'referred_by'        => $rd['referred_by'],
                    'reason_category'    => $rd['reason_category'],
                ],
                $rd
            );
        }

        // ── 10. Referral Interventions ────────────────────────────────────────────

        ReferralIntervention::firstOrCreate(
            ['referral_id' => $referrals[0]->id, 'status_label' => 'Student Contacted'],
            [
                'referral_id'    => $referrals[0]->id,
                'counselor_id'   => $counselor2->id,
                'status_label'   => 'Student Contacted',
                'new_status'     => 'acknowledged',
                'internal_notes' => 'Initial contact with student. Scheduled counseling session for April 2.',
            ]
        );

        ReferralIntervention::firstOrCreate(
            ['referral_id' => $referrals[0]->id, 'status_label' => 'Counseling Ongoing'],
            [
                'referral_id'    => $referrals[0]->id,
                'counselor_id'   => $counselor2->id,
                'status_label'   => 'Counseling Ongoing',
                'new_status'     => 'in_progress',
                'internal_notes' => 'Counseling session completed. Referred to scholarship office for financial aid options. Monitor attendance.',
            ]
        );

        ReferralIntervention::firstOrCreate(
            ['referral_id' => $referrals[1]->id, 'status_label' => 'Student Contacted'],
            [
                'referral_id'    => $referrals[1]->id,
                'counselor_id'   => $counselor2->id,
                'status_label'   => 'Student Contacted',
                'new_status'     => 'in_progress',
                'internal_notes' => 'Student contacted. Shared concerns re: group conflict. Session scheduled for April 10.',
            ]
        );

        // ── 11. Disciplinary Records ──────────────────────────────────────────────

        DisciplinaryRecord::firstOrCreate(
            ['student_profile_id' => $profiles[6]->id, 'incident_date' => '2026-04-08'],
            [
                'student_profile_id' => $profiles[6]->id,
                'reported_by'        => $faculty2->id,
                'handled_by'         => $counselor2->id,
                'offense_type'       => 'minor',
                'offense_category'   => 'misconduct',
                'incident_date'      => '2026-04-08',
                'description'        => 'Engaged in verbal altercation with classmates during group work. Used inappropriate language.',
                'action_taken'       => 'Verbal warning issued. Referred to guidance counselor.',
                'status'             => 'resolved',
                'sanction'           => 'Verbal Warning',
            ]
        );

        DisciplinaryRecord::firstOrCreate(
            ['student_profile_id' => $profiles[2]->id, 'incident_date' => '2026-03-25'],
            [
                'student_profile_id' => $profiles[2]->id,
                'reported_by'        => $faculty1->id,
                'handled_by'         => null,
                'offense_type'       => 'minor',
                'offense_category'   => 'absences',
                'incident_date'      => '2026-03-25',
                'description'        => 'Student has exceeded allowable absences (8 out of 18 sessions) in Marketing Management subject.',
                'action_taken'       => null,
                'status'             => 'pending',
            ]
        );

        // ── 12. Test Results ──────────────────────────────────────────────────────

        $resultData = [
            [
                'student_profile_id'    => $profiles[0]->id,
                'psychological_test_id' => $tests[0]->id,
                'test_schedule_id'      => $sched1->id,
                'recorded_by'           => $counselor1->id,
                'raw_score'             => 62,
                'percentile'            => 75.50,
                'grade_equivalent'      => null,
                'interpretation_level'  => 'above_average',
                'interpretation'        => 'Juan demonstrates above-average reasoning ability. Strong logical sequencing and verbal comprehension skills noted. Recommended for honors track consideration.',
                'career_matches'        => ['Software Engineering', 'Data Analytics', 'Architecture'],
                'test_date'             => '2026-03-10',
                'is_released'           => true,
            ],
            [
                'student_profile_id'    => $profiles[1]->id,
                'psychological_test_id' => $tests[0]->id,
                'test_schedule_id'      => $sched1->id,
                'recorded_by'           => $counselor1->id,
                'raw_score'             => 70,
                'percentile'            => 90.00,
                'grade_equivalent'      => null,
                'interpretation_level'  => 'superior',
                'interpretation'        => 'Maria shows superior cognitive ability with exceptional verbal and quantitative reasoning. High likelihood of success in licensure examinations.',
                'career_matches'        => ['Nursing Administration', 'Medical Education', 'Clinical Research'],
                'test_date'             => '2026-03-10',
                'is_released'           => true,
            ],
            [
                'student_profile_id'    => $profiles[4]->id,
                'psychological_test_id' => $tests[2]->id,
                'test_schedule_id'      => $sched2->id,
                'recorded_by'           => $counselor2->id,
                'raw_score'             => 48,
                'percentile'            => 82.00,
                'grade_equivalent'      => null,
                'interpretation_level'  => 'above_average',
                'interpretation'        => 'Miguel\'s RIASEC profile is Investigative-Conventional-Realistic (ICR). Strong alignment with technology and research-based careers.',
                'career_matches'        => ['Software Developer', 'Systems Analyst', 'IT Consultant', 'Database Administrator'],
                'test_date'             => '2026-04-05',
                'is_released'           => true,
            ],
            [
                'student_profile_id'    => $profiles[3]->id,
                'psychological_test_id' => $tests[2]->id,
                'test_schedule_id'      => $sched2->id,
                'recorded_by'           => $counselor1->id,
                'raw_score'             => 52,
                'percentile'            => 88.00,
                'grade_equivalent'      => null,
                'interpretation_level'  => 'superior',
                'interpretation'        => 'Liza\'s profile is Social-Artistic-Enterprising (SAE). Excellent match for education, communication, and leadership roles.',
                'career_matches'        => ['Secondary Education Teacher', 'School Counselor', 'Curriculum Developer', 'HR Specialist'],
                'test_date'             => '2026-04-05',
                'is_released'           => true,
            ],
            [
                'student_profile_id'    => $profiles[2]->id,
                'psychological_test_id' => $tests[3]->id,
                'test_schedule_id'      => null,
                'recorded_by'           => $counselor2->id,
                'raw_score'             => 18,
                'percentile'            => null,
                'grade_equivalent'      => null,
                'interpretation_level'  => 'low',
                'interpretation'        => 'BAI score of 18 indicates moderate anxiety level. Student experiences significant worry about financial stability and academic performance. Counseling and coping strategies recommended.',
                'career_matches'        => [],
                'test_date'             => '2026-04-02',
                'is_released'           => false,
            ],
            [
                'student_profile_id'    => $profiles[7]->id,
                'psychological_test_id' => $tests[0]->id,
                'test_schedule_id'      => $sched1->id,
                'recorded_by'           => $counselor2->id,
                'raw_score'             => 58,
                'percentile'            => 68.00,
                'grade_equivalent'      => null,
                'interpretation_level'  => 'average',
                'interpretation'        => 'Sophia demonstrates average ability with consistent performance across verbal and quantitative domains. Steady and reliable learner.',
                'career_matches'        => ['Nursing Practice', 'Community Health', 'Medical Technology'],
                'test_date'             => '2026-03-10',
                'is_released'           => true,
            ],
        ];

        foreach ($resultData as $rd) {
            TestResult::firstOrCreate(
                [
                    'student_profile_id'    => $rd['student_profile_id'],
                    'psychological_test_id' => $rd['psychological_test_id'],
                    'test_date'             => $rd['test_date'],
                ],
                $rd
            );
        }

        // ── 13. Clearance Requests ────────────────────────────────────────────────

        $clearanceData = [
            [
                'student_profile_id' => $profiles[1]->id,
                'processed_by'       => $counselor1->id,
                'clearance_type'     => 'graduation',
                'academic_year'      => '2025-2026',
                'semester'           => '2nd',
                'purpose'            => 'Graduation clearance for NLE registration',
                'status'             => 'approved',
                'notes'              => 'Exit interview completed. All guidance requirements fulfilled.',
                'processed_at'       => now()->subDays(5),
            ],
            [
                'student_profile_id' => $profiles[4]->id,
                'processed_by'       => null,
                'clearance_type'     => 'graduation',
                'academic_year'      => '2025-2026',
                'semester'           => '2nd',
                'purpose'            => 'Graduation clearance for commencement ceremony',
                'status'             => 'for_exit_survey',
                'notes'              => null,
                'processed_at'       => null,
            ],
            [
                'student_profile_id' => $profiles[7]->id,
                'processed_by'       => null,
                'clearance_type'     => 'graduation',
                'academic_year'      => '2025-2026',
                'semester'           => '2nd',
                'purpose'            => 'Graduation clearance for diploma release',
                'status'             => 'pending',
                'notes'              => null,
                'processed_at'       => null,
            ],
            [
                'student_profile_id' => $profiles[3]->id,
                'processed_by'       => $counselor1->id,
                'clearance_type'     => 'scholarship',
                'academic_year'      => '2025-2026',
                'semester'           => '1st',
                'purpose'            => 'CHED scholarship renewal clearance',
                'status'             => 'approved',
                'notes'              => 'Good standing. Cleared.',
                'processed_at'       => now()->subDays(60),
            ],
        ];

        $clearances = [];
        foreach ($clearanceData as $cd) {
            $clearances[] = ClearanceRequest::firstOrCreate(
                [
                    'student_profile_id' => $cd['student_profile_id'],
                    'clearance_type'     => $cd['clearance_type'],
                    'academic_year'      => $cd['academic_year'],
                    'semester'           => $cd['semester'],
                ],
                $cd
            );
        }

        // ── 14. Good Moral Certificates ───────────────────────────────────────────

        GoodMoralCertificate::firstOrCreate(
            ['certificate_number' => 'GMC-2026-00001'],
            [
                'student_profile_id'   => $profiles[1]->id,
                'issued_by'            => $counselor1->id,
                'clearance_request_id' => $clearances[0]->id,
                'certificate_number'   => 'GMC-2026-00001',
                'purpose'              => 'NLE Registration Requirement',
                'validity_months'      => 6,
                'issued_at'            => now()->subDays(5),
                'is_revoked'           => false,
            ]
        );

        GoodMoralCertificate::firstOrCreate(
            ['certificate_number' => 'GMC-2026-00002'],
            [
                'student_profile_id'   => $profiles[3]->id,
                'issued_by'            => $counselor1->id,
                'clearance_request_id' => $clearances[3]->id,
                'certificate_number'   => 'GMC-2026-00002',
                'purpose'              => 'CHED Scholarship Renewal',
                'validity_months'      => 12,
                'issued_at'            => now()->subDays(60),
                'is_revoked'           => false,
            ]
        );

        GoodMoralCertificate::firstOrCreate(
            ['certificate_number' => 'GMC-2025-00035'],
            [
                'student_profile_id'   => $profiles[0]->id,
                'issued_by'            => $counselor1->id,
                'clearance_request_id' => null,
                'certificate_number'   => 'GMC-2025-00035',
                'purpose'              => 'Employment Application',
                'validity_months'      => 6,
                'issued_at'            => now()->subDays(120),
                'is_revoked'           => true,
                'revoked_reason'       => 'Expired — validity period elapsed.',
            ]
        );
    }
}
