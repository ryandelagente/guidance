<?php

namespace App\Http\Controllers;

use App\Models\EmergencyContact;
use App\Models\StudentDocument;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentProfileController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentProfile::with(['user', 'assignedCounselor'])
            ->orderBy('last_name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('student_id_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('college')) {
            $query->where('college', $request->college);
        }

        if ($request->filled('academic_status')) {
            $query->where('academic_status', $request->academic_status);
        }

        $profiles = $query->paginate(20)->withQueryString();
        $colleges  = StudentProfile::distinct()->pluck('college')->filter()->sort()->values();

        return view('students.index', compact('profiles', 'colleges'));
    }

    public function create()
    {
        $counselors = User::where('role', 'guidance_counselor')->where('is_active', true)->get();
        return view('students.create', compact('counselors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name'            => 'required|string|max:100',
            'middle_name'           => 'nullable|string|max:100',
            'last_name'             => 'required|string|max:100',
            'suffix'                => 'nullable|string|max:10',
            'date_of_birth'         => 'nullable|date|before:today',
            'sex'                   => 'nullable|in:male,female',
            'civil_status'          => 'nullable|string|max:50',
            'religion'              => 'nullable|string|max:100',
            'nationality'           => 'nullable|string|max:100',
            'contact_number'        => 'nullable|string|max:20',
            'home_address'          => 'nullable|string',
            'student_id_number'     => 'nullable|string|unique:student_profiles,student_id_number',
            'college'               => 'nullable|string|max:150',
            'program'               => 'nullable|string|max:150',
            'year_level'            => 'nullable|string|max:20',
            'student_type'          => 'required|in:regular,irregular,transferee,returnee',
            'scholarship'           => 'nullable|string|max:150',
            'academic_status'       => 'required|in:good_standing,probation,at_risk,dismissed',
            'father_name'           => 'nullable|string|max:200',
            'father_occupation'     => 'nullable|string|max:150',
            'father_contact'        => 'nullable|string|max:20',
            'mother_name'           => 'nullable|string|max:200',
            'mother_occupation'     => 'nullable|string|max:150',
            'mother_contact'        => 'nullable|string|max:20',
            'parents_status'        => 'nullable|in:married,separated,widowed,single_parent,deceased',
            'guardian_name'         => 'nullable|string|max:200',
            'guardian_relationship' => 'nullable|string|max:100',
            'guardian_contact'      => 'nullable|string|max:20',
            'monthly_family_income' => 'nullable|string|max:50',
            'is_pwd'                => 'boolean',
            'pwd_details'           => 'nullable|string',
            'is_working_student'    => 'boolean',
            'assigned_counselor_id' => 'nullable|exists:users,id',
            'profile_photo'         => 'nullable|image|max:2048',
            // Emergency contacts
            'emergency_contacts'             => 'nullable|array',
            'emergency_contacts.*.name'      => 'required_with:emergency_contacts|string|max:200',
            'emergency_contacts.*.relationship' => 'required_with:emergency_contacts|string|max:100',
            'emergency_contacts.*.contact_number' => 'required_with:emergency_contacts|string|max:20',
        ]);

        // Create a linked user account for the student
        $user = User::create([
            'name'      => "{$data['first_name']} {$data['last_name']}",
            'email'     => $data['student_id_number']
                           ? strtolower($data['student_id_number']) . '@chmsu.edu.ph'
                           : strtolower($data['first_name'] . '.' . $data['last_name']) . '@chmsu.edu.ph',
            'password'  => bcrypt('Welcome@CHMSU!'),
            'role'      => 'student',
            'student_id' => $data['student_id_number'] ?? null,
        ]);

        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $profile = $user->studentProfile()->create(array_merge(
            collect($data)->except(['emergency_contacts', 'profile_photo'])->toArray(),
            ['profile_photo' => $data['profile_photo'] ?? null]
        ));

        if (!empty($data['emergency_contacts'])) {
            foreach ($data['emergency_contacts'] as $i => $contact) {
                $profile->emergencyContacts()->create(array_merge($contact, [
                    'is_primary' => $i === 0,
                ]));
            }
        }

        return redirect()->route('students.show', $profile)
            ->with('success', 'Student profile created successfully.');
    }

    public function show(StudentProfile $student)
    {
        $student->load(['user', 'assignedCounselor', 'documents', 'emergencyContacts']);
        return view('students.show', compact('student'));
    }

    public function cumulativeRecord(StudentProfile $student)
    {
        $student->load([
            'user',
            'assignedCounselor',
            'emergencyContacts',
            'documents',
            'appointments.counselor',
            'counselingSessions.counselor',
            'referrals.referredBy',
            'disciplinaryRecords',
            'testResults.test',
            'clearanceRequests',
            'certificates',
        ]);

        \App\Models\AuditLog::record(
            action: 'export',
            subject: $student,
            description: "Generated cumulative record PDF for {$student->full_name}",
        );

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('students.cumulative-record', compact('student'))
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans');

        $filename = 'CR-' . preg_replace('/[^A-Za-z0-9]+/', '-', $student->full_name) . '-' . now()->format('Ymd') . '.pdf';
        return $pdf->download($filename);
    }

    public function timeline(StudentProfile $student)
    {
        $student->load('assignedCounselor');

        $events = collect();

        // Appointments
        foreach ($student->appointments()->with('counselor')->get() as $a) {
            $events->push([
                'date'     => $a->appointment_date,
                'time'     => substr($a->start_time, 0, 5),
                'type'     => 'appointment',
                'icon'     => '📅',
                'color'    => 'blue',
                'title'    => 'Appointment — ' . ucwords(str_replace('_', ' ', $a->appointment_type)),
                'subtitle' => 'with ' . ($a->counselor?->name ?? 'Counselor'),
                'status'   => ucwords(str_replace('_', ' ', $a->status)),
                'url'      => route('appointments.show', $a),
            ]);
        }

        // Counseling sessions
        foreach ($student->counselingSessions()->with('counselor')->get() as $s) {
            $events->push([
                'date'     => $s->created_at->toDateString(),
                'time'     => $s->created_at->format('H:i'),
                'type'     => 'session',
                'icon'     => '📝',
                'color'    => 'purple',
                'title'    => 'Counseling Session',
                'subtitle' => 'Concern: ' . ucwords(str_replace('_', ' ', $s->presenting_concern ?? 'Not specified')),
                'status'   => ucwords(str_replace('_', ' ', $s->session_status ?? '')),
                'url'      => route('sessions.show', $s),
            ]);
        }

        // Referrals
        foreach ($student->referrals()->with('referredBy')->get() as $r) {
            $events->push([
                'date'     => $r->created_at->toDateString(),
                'time'     => $r->created_at->format('H:i'),
                'type'     => 'referral',
                'icon'     => '🚩',
                'color'    => 'orange',
                'title'    => 'Referral — ' . ucwords(str_replace('_', ' ', $r->reason_category)),
                'subtitle' => 'Submitted by ' . ($r->referredBy?->name ?? 'Faculty') . ' • ' . ucfirst($r->urgency) . ' urgency',
                'status'   => ucwords(str_replace('_', ' ', $r->status)),
                'url'      => route('referrals.show', $r),
            ]);
        }

        // Disciplinary records
        foreach ($student->disciplinaryRecords()->get() as $d) {
            $events->push([
                'date'     => $d->incident_date,
                'time'     => null,
                'type'     => 'disciplinary',
                'icon'     => '⚠️',
                'color'    => 'red',
                'title'    => ucfirst($d->offense_type) . ' offense — ' . $d->offense_category,
                'subtitle' => \Illuminate\Support\Str::limit($d->description, 100),
                'status'   => ucwords(str_replace('_', ' ', $d->status)),
                'url'      => route('disciplinary.show', $d),
            ]);
        }

        // Test results
        foreach ($student->testResults()->with('test')->get() as $t) {
            $events->push([
                'date'     => $t->test_date,
                'time'     => null,
                'type'     => 'test_result',
                'icon'     => '🧪',
                'color'    => 'indigo',
                'title'    => 'Test: ' . ($t->test?->test_name ?? 'Psychological Test'),
                'subtitle' => 'Result: ' . ($t->interpretation_level ?? 'Recorded'),
                'status'   => $t->is_released ? 'Released' : 'Pending',
                'url'      => route('test-results.show', $t),
            ]);
        }

        // Clearance requests
        foreach ($student->clearanceRequests()->get() as $c) {
            $events->push([
                'date'     => $c->created_at->toDateString(),
                'time'     => $c->created_at->format('H:i'),
                'type'     => 'clearance',
                'icon'     => '✅',
                'color'    => 'green',
                'title'    => ucfirst($c->clearance_type) . ' Clearance Request',
                'subtitle' => $c->purpose ?? '',
                'status'   => ucwords(str_replace('_', ' ', $c->status)),
                'url'      => route('clearance.show', $c),
            ]);
        }

        // Good moral certificates
        foreach ($student->certificates()->get() as $cert) {
            $events->push([
                'date'     => optional($cert->issued_at)->toDateString() ?? $cert->created_at->toDateString(),
                'time'     => null,
                'type'     => 'certificate',
                'icon'     => '🎓',
                'color'    => 'emerald',
                'title'    => 'Good Moral Certificate Issued',
                'subtitle' => 'Cert. No. ' . $cert->certificate_number . ' • ' . $cert->purpose,
                'status'   => $cert->is_revoked ? 'Revoked' : 'Active',
                'url'      => route('certificates.show', $cert),
            ]);
        }

        // Documents uploaded
        foreach ($student->documents as $doc) {
            $events->push([
                'date'     => $doc->created_at->toDateString(),
                'time'     => $doc->created_at->format('H:i'),
                'type'     => 'document',
                'icon'     => '📎',
                'color'    => 'gray',
                'title'    => 'Document Uploaded: ' . $doc->document_type,
                'subtitle' => $doc->file_name,
                'status'   => null,
                'url'      => null,
            ]);
        }

        // Sort newest first, group by date
        $events = $events->sortByDesc(fn ($e) => ($e['date'] instanceof \Carbon\Carbon ? $e['date']->toDateString() : $e['date']) . ' ' . ($e['time'] ?? '00:00'))
                         ->values();

        $grouped = $events->groupBy(fn ($e) => $e['date'] instanceof \Carbon\Carbon ? $e['date']->format('Y-m-d') : $e['date']);

        return view('students.timeline', compact('student', 'grouped', 'events'));
    }

    public function edit(StudentProfile $student)
    {
        $counselors = User::where('role', 'guidance_counselor')->where('is_active', true)->get();
        $student->load(['emergencyContacts']);
        return view('students.edit', compact('student', 'counselors'));
    }

    public function update(Request $request, StudentProfile $student)
    {
        $data = $request->validate([
            'first_name'            => 'required|string|max:100',
            'middle_name'           => 'nullable|string|max:100',
            'last_name'             => 'required|string|max:100',
            'suffix'                => 'nullable|string|max:10',
            'date_of_birth'         => 'nullable|date|before:today',
            'sex'                   => 'nullable|in:male,female',
            'civil_status'          => 'nullable|string|max:50',
            'religion'              => 'nullable|string|max:100',
            'nationality'           => 'nullable|string|max:100',
            'contact_number'        => 'nullable|string|max:20',
            'home_address'          => 'nullable|string',
            'student_id_number'     => "nullable|string|unique:student_profiles,student_id_number,{$student->id}",
            'college'               => 'nullable|string|max:150',
            'program'               => 'nullable|string|max:150',
            'year_level'            => 'nullable|string|max:20',
            'student_type'          => 'required|in:regular,irregular,transferee,returnee',
            'scholarship'           => 'nullable|string|max:150',
            'academic_status'       => 'required|in:good_standing,probation,at_risk,dismissed',
            'father_name'           => 'nullable|string|max:200',
            'father_occupation'     => 'nullable|string|max:150',
            'father_contact'        => 'nullable|string|max:20',
            'mother_name'           => 'nullable|string|max:200',
            'mother_occupation'     => 'nullable|string|max:150',
            'mother_contact'        => 'nullable|string|max:20',
            'parents_status'        => 'nullable|in:married,separated,widowed,single_parent,deceased',
            'guardian_name'         => 'nullable|string|max:200',
            'guardian_relationship' => 'nullable|string|max:100',
            'guardian_contact'      => 'nullable|string|max:20',
            'monthly_family_income' => 'nullable|string|max:50',
            'is_pwd'                => 'boolean',
            'pwd_details'           => 'nullable|string',
            'is_working_student'    => 'boolean',
            'assigned_counselor_id' => 'nullable|exists:users,id',
            'profile_photo'         => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($student->profile_photo) {
                Storage::disk('public')->delete($student->profile_photo);
            }
            $data['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $student->update($data);

        return redirect()->route('students.show', $student)
            ->with('success', 'Student profile updated successfully.');
    }

    public function destroy(StudentProfile $student)
    {
        if ($student->profile_photo) {
            Storage::disk('public')->delete($student->profile_photo);
        }
        $student->user()->delete();
        return redirect()->route('students.index')
            ->with('success', 'Student record deleted.');
    }

    // ── Document upload ───────────────────────────────────────────────────────

    public function uploadDocument(Request $request, StudentProfile $student)
    {
        $data = $request->validate([
            'document_type' => 'required|string|max:100',
            'document_file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx',
        ]);

        $file = $request->file('document_file');
        $path = $file->store("student-documents/{$student->id}", 'public');

        StudentDocument::create([
            'student_profile_id' => $student->id,
            'document_type'      => $data['document_type'],
            'file_name'          => $file->getClientOriginalName(),
            'file_path'          => $path,
            'mime_type'          => $file->getMimeType(),
            'file_size'          => $file->getSize(),
            'uploaded_by'        => $request->user()->id,
        ]);

        return back()->with('success', 'Document uploaded.');
    }

    public function downloadDocument(StudentProfile $student, StudentDocument $document)
    {
        abort_unless($document->student_profile_id === $student->id, 404);
        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    public function deleteDocument(StudentProfile $student, StudentDocument $document)
    {
        abort_unless($document->student_profile_id === $student->id, 404);
        Storage::disk('public')->delete($document->file_path);
        $document->delete();
        return back()->with('success', 'Document removed.');
    }

    // ── Emergency contacts ────────────────────────────────────────────────────

    public function addEmergencyContact(Request $request, StudentProfile $student)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:200',
            'relationship'   => 'required|string|max:100',
            'contact_number' => 'required|string|max:20',
            'address'        => 'nullable|string|max:500',
        ]);

        $isPrimary = $student->emergencyContacts()->count() === 0;

        $student->emergencyContacts()->create(array_merge($data, ['is_primary' => $isPrimary]));

        return back()->with('success', 'Emergency contact added.');
    }

    public function deleteEmergencyContact(StudentProfile $student, EmergencyContact $contact)
    {
        abort_unless($contact->student_profile_id === $student->id, 404);
        $contact->delete();
        return back()->with('success', 'Emergency contact removed.');
    }
}
