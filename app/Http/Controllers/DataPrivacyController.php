<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\CounselingSession;
use App\Models\GoodMoralCertificate;
use App\Models\Referral;
use App\Models\StudentProfile;
use App\Models\WellnessCheckin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class DataPrivacyController extends Controller
{
    /**
     * Student-facing dashboard for data subject rights (PH-DPA / RA 10173).
     */
    public function index(Request $request)
    {
        $user = $request->user();
        abort_unless($user->isStudent(), 403);
        $profile = $user->studentProfile;
        abort_unless($profile, 403);

        $summary = $this->buildSummary($profile);

        // Show last 30 audit log entries that involve this student's profile or this user
        $recentAccesses = AuditLog::with('user')
            ->where(function ($q) use ($user, $profile) {
                $q->where(fn ($q2) => $q2->where('auditable_type', 'App\\Models\\StudentProfile')->where('auditable_id', $profile->id))
                  ->orWhere(fn ($q2) => $q2->where('auditable_type', 'App\\Models\\User')->where('auditable_id', $user->id));
            })
            ->whereIn('action', ['viewed','updated','created','deleted','login','logout','export'])
            ->latest()
            ->limit(30)
            ->get();

        return view('data-privacy.index', compact('profile', 'summary', 'recentAccesses'));
    }

    /**
     * Download all data we have about the user as JSON.
     */
    public function download(Request $request)
    {
        $user = $request->user();
        abort_unless($user->isStudent(), 403);
        $profile = $user->studentProfile;
        abort_unless($profile, 403);

        $profile->load(['emergencyContacts', 'documents', 'appointments.counselor']);

        $data = [
            'exported_at' => now()->toIso8601String(),
            'about' => 'This is a complete export of all personal data CHMSU GMS holds about you, in machine-readable JSON. Generated on your request under RA 10173 (Data Privacy Act).',
            'account' => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->role,
                'created_at' => $user->created_at?->toIso8601String(),
                'is_active'  => $user->is_active,
            ],
            'student_profile' => [
                'student_id_number'      => $profile->student_id_number,
                'first_name'             => $profile->first_name,
                'middle_name'            => $profile->middle_name,
                'last_name'              => $profile->last_name,
                'suffix'                 => $profile->suffix,
                'date_of_birth'          => optional($profile->date_of_birth)->toDateString(),
                'sex'                    => $profile->sex,
                'civil_status'           => $profile->civil_status,
                'religion'               => $profile->religion,
                'nationality'            => $profile->nationality,
                'contact_number'         => $profile->contact_number,
                'home_address'           => $profile->home_address,
                'college'                => $profile->college,
                'program'                => $profile->program,
                'year_level'             => $profile->year_level,
                'student_type'           => $profile->student_type,
                'scholarship'            => $profile->scholarship,
                'academic_status'        => $profile->academic_status,
                'father_name'            => $profile->father_name,
                'father_occupation'      => $profile->father_occupation,
                'father_contact'         => $profile->father_contact,
                'mother_name'            => $profile->mother_name,
                'mother_occupation'      => $profile->mother_occupation,
                'mother_contact'         => $profile->mother_contact,
                'parents_status'         => $profile->parents_status,
                'guardian_name'          => $profile->guardian_name,
                'guardian_relationship'  => $profile->guardian_relationship,
                'guardian_contact'       => $profile->guardian_contact,
                'monthly_family_income'  => $profile->monthly_family_income,
                'is_pwd'                 => $profile->is_pwd,
                'pwd_details'            => $profile->pwd_details,
                'is_working_student'     => $profile->is_working_student,
                'assigned_counselor'     => optional($profile->assignedCounselor)->name,
            ],
            'emergency_contacts' => $profile->emergencyContacts->map(fn ($c) => [
                'name'           => $c->name,
                'relationship'   => $c->relationship,
                'contact_number' => $c->contact_number,
                'address'        => $c->address,
                'is_primary'     => $c->is_primary,
            ])->values(),
            'documents' => $profile->documents->map(fn ($d) => [
                'document_type' => $d->document_type,
                'file_name'     => $d->file_name,
                'uploaded_at'   => $d->created_at->toIso8601String(),
            ])->values(),
            'appointments' => $profile->appointments->map(fn ($a) => [
                'date'      => $a->appointment_date->toDateString(),
                'time'      => substr($a->start_time, 0, 5),
                'type'      => $a->appointment_type,
                'mode'      => $a->meeting_type,
                'counselor' => optional($a->counselor)->name,
                'status'    => $a->status,
                'concern'   => $a->student_concern,
            ])->values(),
            'counseling_sessions_count' => CounselingSession::where('student_profile_id', $profile->id)->count(),
            'counseling_sessions_note'  => 'For confidentiality reasons, the body of counseling case notes is encrypted with a counselor PIN and is only viewable by counselors via the GMS interface. Per RA 10173, you can request a printed transcript of your case notes by contacting the Guidance Office in person.',
            'referrals' => Referral::where('student_profile_id', $profile->id)->get()->map(fn ($r) => [
                'date'        => $r->created_at->toDateString(),
                'category'    => $r->reason_category,
                'urgency'     => $r->urgency,
                'status'      => $r->status,
                'description' => $r->description,
            ])->values(),
            'wellness_checkins' => WellnessCheckin::where('student_profile_id', $profile->id)->get()->map(fn ($c) => [
                'date'             => $c->created_at->toDateString(),
                'mood'             => $c->mood,
                'stress_level'     => $c->stress_level,
                'sleep_quality'    => $c->sleep_quality,
                'academic_stress'  => $c->academic_stress,
                'wants_counselor'  => $c->wants_counselor,
                'notes'            => $c->notes,
            ])->values(),
            'certificates' => GoodMoralCertificate::where('student_profile_id', $profile->id)->get()->map(fn ($c) => [
                'certificate_number' => $c->certificate_number,
                'purpose'            => $c->purpose,
                'issued_at'          => optional($c->issued_at)->toDateString(),
                'is_revoked'         => $c->is_revoked,
            ])->values(),
        ];

        AuditLog::record(
            action: 'export',
            subject: $profile,
            description: 'Student requested personal data export (RA 10173)',
        );

        $filename = 'chmsu-gms-my-data-' . now()->format('Ymd-His') . '.json';

        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $filename, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Submit a data correction request — emails the assigned counselor.
     */
    public function requestCorrection(Request $request)
    {
        $user = $request->user();
        abort_unless($user->isStudent(), 403);
        $profile = $user->studentProfile;
        abort_unless($profile, 403);

        $data = $request->validate([
            'field'   => 'required|string|max:100',
            'current' => 'nullable|string|max:500',
            'desired' => 'required|string|max:500',
            'reason'  => 'nullable|string|max:1000',
        ]);

        AuditLog::record(
            action: 'created',
            subject: $profile,
            description: 'Student submitted data correction request',
            changes: [
                'field'   => $data['field'],
                'current' => $data['current'] ?? '(blank)',
                'desired' => $data['desired'],
                'reason'  => $data['reason'] ?? '',
            ],
        );

        // Send email to assigned counselor (or director if unassigned)
        $recipient = $profile->assignedCounselor?->email
                  ?? \App\Models\User::where('role', 'guidance_director')->first()?->email;

        if ($recipient) {
            try {
                Mail::raw(
                    "Student: {$profile->full_name} ({$profile->student_id_number})\n\n" .
                    "Field to correct: {$data['field']}\n" .
                    "Current value: " . ($data['current'] ?? '(blank)') . "\n" .
                    "Desired value: {$data['desired']}\n" .
                    "Reason: " . ($data['reason'] ?? '(none provided)') . "\n\n" .
                    "Submitted via the GMS Data Privacy module on " . now()->format('F d, Y h:i A'),
                    function ($m) use ($recipient, $profile) {
                        $m->to($recipient)
                          ->subject("Data correction request — {$profile->full_name}");
                    }
                );
            } catch (\Throwable $e) {
                // Mail failed — request is still logged in audit trail
            }
        }

        return back()->with('success', 'Correction request submitted. Your counselor will review and respond within 7 working days. The request is also logged in your access trail.');
    }

    private function buildSummary(StudentProfile $profile): array
    {
        return [
            'appointments'         => $profile->appointments()->count(),
            'sessions'             => CounselingSession::where('student_profile_id', $profile->id)->count(),
            'referrals'            => Referral::where('student_profile_id', $profile->id)->count(),
            'wellness_checkins'    => WellnessCheckin::where('student_profile_id', $profile->id)->count(),
            'documents'            => $profile->documents()->count(),
            'emergency_contacts'   => $profile->emergencyContacts()->count(),
            'certificates'         => GoodMoralCertificate::where('student_profile_id', $profile->id)->count(),
        ];
    }
}
