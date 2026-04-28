<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cumulative Record — {{ $student->full_name }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9.5pt; color: #111; }
        @page { size: A4 portrait; margin: 14mm 14mm 14mm 14mm; }

        .letterhead { text-align: center; border-bottom: 3px double #1a3a5c; padding-bottom: 10px; margin-bottom: 10px; }
        .letterhead .republic { font-size: 8.5pt; color: #555; }
        .letterhead .university { font-size: 13pt; font-weight: bold; color: #1a3a5c; letter-spacing: 0.5px; }
        .letterhead .address { font-size: 8.5pt; color: #666; margin-top: 1px; }
        .letterhead .office { font-size: 10pt; font-weight: bold; color: #1a3a5c; margin-top: 5px; letter-spacing: 0.5px; }
        .letterhead .doc-title { font-size: 11pt; font-weight: bold; margin-top: 4px; text-transform: uppercase; letter-spacing: 1px; }

        h2 {
            font-size: 9.5pt;
            color: #fff;
            background: #1a3a5c;
            padding: 3px 8px;
            margin: 12px 0 5px;
            letter-spacing: 0.5px;
        }
        h3 { font-size: 9pt; color: #1a3a5c; margin: 8px 0 3px; border-bottom: 1px solid #c8d6e5; padding-bottom: 1px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        td { padding: 2.5px 5px; vertical-align: top; }
        .field-table td { font-size: 9pt; }
        .field-label { color: #555; width: 35%; }
        .field-value { color: #111; font-weight: 500; }

        .records-table { font-size: 8.5pt; }
        .records-table thead tr { background: #1a3a5c; }
        .records-table th { color: #fff; padding: 3px 5px; text-align: left; font-size: 8pt; font-weight: bold; }
        .records-table td { padding: 2.5px 5px; border-bottom: 1px solid #e8ecf0; }
        .records-table tr:nth-child(even) td { background: #f7f8fa; }

        .photo-box {
            width: 90px;
            height: 110px;
            border: 1px solid #999;
            background: #f0f0f0;
            text-align: center;
            font-size: 8pt;
            color: #999;
            display: inline-block;
        }
        .empty { color: #aaa; font-style: italic; font-size: 8.5pt; }

        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-good { background: #d4edda; color: #155724; }
        .badge-prob { background: #fff3cd; color: #856404; }
        .badge-risk { background: #f8d7da; color: #721c24; }
        .badge-info { background: #cce5ff; color: #004085; }

        .footer { margin-top: 15px; padding-top: 6px; border-top: 1px solid #ddd; font-size: 7.5pt; color: #999; text-align: center; }
        .sig-grid { display: table; width: 100%; margin-top: 25px; }
        .sig-cell { display: table-cell; width: 50%; text-align: center; padding: 0 8px; }
        .sig-line { border-top: 1px solid #333; margin-top: 30px; padding-top: 2px; font-size: 8.5pt; }
        .sig-name { font-weight: bold; }
        .sig-role { font-size: 8pt; color: #666; }

        .page-break { page-break-before: always; }
    </style>
</head>
<body>

{{-- ── Letterhead ── --}}
<div class="letterhead">
    <div class="republic">Republic of the Philippines</div>
    <div class="university">Carlos Hilado Memorial State University</div>
    <div class="address">Talisay City, Negros Occidental</div>
    <div class="office">Guidance and Counseling Office</div>
    <div class="doc-title">Student Cumulative Record</div>
    <div style="font-size: 8pt; color: #666; margin-top: 3px;">
        Generated: {{ now()->format('F d, Y') }}
        @if($student->student_id_number) &bull; Student ID: <strong>{{ $student->student_id_number }}</strong> @endif
    </div>
</div>

{{-- ── Identification ── --}}
<h2>I. Student Identification</h2>
<table>
    <tr>
        <td style="width: 105px; padding: 0;">
            <div class="photo-box">
                @if($student->profile_photo)
                    <img src="{{ public_path('storage/' . $student->profile_photo) }}" style="width:100%; height:100%; object-fit:cover;">
                @else
                    <span style="line-height: 110px;">No photo</span>
                @endif
            </div>
        </td>
        <td>
            <table class="field-table">
                <tr><td class="field-label">Full Name</td><td class="field-value">{{ $student->full_name }}</td></tr>
                <tr><td class="field-label">Date of Birth</td><td class="field-value">{{ optional($student->date_of_birth)->format('F d, Y') ?? '—' }} {{ $student->age ? '(' . $student->age . ' yrs)' : '' }}</td></tr>
                <tr><td class="field-label">Sex</td><td class="field-value">{{ ucfirst($student->sex ?? '—') }}</td></tr>
                <tr><td class="field-label">Civil Status</td><td class="field-value">{{ $student->civil_status ?? '—' }}</td></tr>
                <tr><td class="field-label">Religion / Nationality</td><td class="field-value">{{ $student->religion ?? '—' }} / {{ $student->nationality ?? '—' }}</td></tr>
                <tr><td class="field-label">Contact Number</td><td class="field-value">{{ $student->contact_number ?? '—' }}</td></tr>
                <tr><td class="field-label">Home Address</td><td class="field-value">{{ $student->home_address ?? '—' }}</td></tr>
            </table>
        </td>
    </tr>
</table>

{{-- ── Academic ── --}}
<h2>II. Academic Information</h2>
<table class="field-table">
    <tr>
        <td class="field-label" style="width: 22%;">College</td><td class="field-value" style="width: 28%;">{{ $student->college ?? '—' }}</td>
        <td class="field-label" style="width: 22%;">Program</td><td class="field-value">{{ $student->program ?? '—' }}</td>
    </tr>
    <tr>
        <td class="field-label">Year Level</td><td class="field-value">{{ $student->year_level ?? '—' }}</td>
        <td class="field-label">Student Type</td><td class="field-value">{{ ucfirst($student->student_type ?? '—') }}</td>
    </tr>
    <tr>
        <td class="field-label">Scholarship</td><td class="field-value">{{ $student->scholarship ?? '—' }}</td>
        <td class="field-label">Academic Status</td>
        <td class="field-value">
            @php
                $badgeClass = match($student->academic_status) {
                    'good_standing' => 'badge-good',
                    'probation'     => 'badge-prob',
                    'at_risk'       => 'badge-risk',
                    'dismissed'     => 'badge-risk',
                    default         => 'badge-info',
                };
            @endphp
            <span class="badge {{ $badgeClass }}">{{ ucwords(str_replace('_', ' ', $student->academic_status ?? 'unknown')) }}</span>
        </td>
    </tr>
    <tr>
        <td class="field-label">Assigned Counselor</td>
        <td class="field-value" colspan="3">{{ $student->assignedCounselor?->name ?? 'Unassigned' }}</td>
    </tr>
</table>

{{-- ── Family ── --}}
<h2>III. Family Background</h2>
<table class="field-table">
    <tr>
        <td class="field-label" style="width: 22%;">Father</td><td class="field-value" style="width: 28%;">{{ $student->father_name ?? '—' }}</td>
        <td class="field-label" style="width: 22%;">Father's Occupation</td><td class="field-value">{{ $student->father_occupation ?? '—' }}</td>
    </tr>
    <tr>
        <td class="field-label">Mother</td><td class="field-value">{{ $student->mother_name ?? '—' }}</td>
        <td class="field-label">Mother's Occupation</td><td class="field-value">{{ $student->mother_occupation ?? '—' }}</td>
    </tr>
    <tr>
        <td class="field-label">Parents' Status</td>
        <td class="field-value">{{ ucwords(str_replace('_', ' ', $student->parents_status ?? '—')) }}</td>
        <td class="field-label">Monthly Family Income</td><td class="field-value">{{ $student->monthly_family_income ?? '—' }}</td>
    </tr>
    <tr>
        <td class="field-label">Guardian</td><td class="field-value">{{ $student->guardian_name ?? '—' }}</td>
        <td class="field-label">Relationship / Contact</td>
        <td class="field-value">{{ $student->guardian_relationship ?? '—' }} / {{ $student->guardian_contact ?? '—' }}</td>
    </tr>
</table>

{{-- ── Special Categories ── --}}
@if($student->is_pwd || $student->is_working_student)
<h2>IV. Special Categories</h2>
<table class="field-table">
    @if($student->is_pwd)
    <tr><td class="field-label" style="width: 25%;">PWD Status</td><td class="field-value"><strong>Yes</strong> — {{ $student->pwd_details ?? 'No details on file' }}</td></tr>
    @endif
    @if($student->is_working_student)
    <tr><td class="field-label">Working Student</td><td class="field-value"><strong>Yes</strong></td></tr>
    @endif
</table>
@endif

{{-- ── Emergency Contacts ── --}}
@if($student->emergencyContacts->isNotEmpty())
<h2>{{ $student->is_pwd || $student->is_working_student ? 'V' : 'IV' }}. Emergency Contacts</h2>
<table class="records-table">
    <thead>
        <tr>
            <th style="width: 25%;">Name</th>
            <th style="width: 18%;">Relationship</th>
            <th style="width: 22%;">Contact Number</th>
            <th>Address</th>
        </tr>
    </thead>
    <tbody>
        @foreach($student->emergencyContacts as $contact)
        <tr>
            <td>{{ $contact->name }} @if($contact->is_primary) <span class="badge badge-info">Primary</span> @endif</td>
            <td>{{ $contact->relationship }}</td>
            <td>{{ $contact->contact_number }}</td>
            <td>{{ $contact->address ?? '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- ─────── Page break before history ─────── --}}
<div class="page-break"></div>

{{-- ── Counseling History ── --}}
<h2>VI. Counseling Appointments</h2>
@if($student->appointments->isEmpty())
    <p class="empty">No appointment records.</p>
@else
<table class="records-table">
    <thead>
        <tr>
            <th style="width: 14%;">Date</th>
            <th style="width: 9%;">Time</th>
            <th style="width: 18%;">Type</th>
            <th style="width: 25%;">Counselor</th>
            <th style="width: 12%;">Mode</th>
            <th style="width: 12%;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($student->appointments->sortByDesc('appointment_date') as $appt)
        <tr>
            <td>{{ $appt->appointment_date->format('M d, Y') }}</td>
            <td>{{ substr($appt->start_time, 0, 5) }}</td>
            <td>{{ ucwords(str_replace('_', ' ', $appt->appointment_type)) }}</td>
            <td>{{ $appt->counselor?->name ?? '—' }}</td>
            <td>{{ ucwords(str_replace('_', ' ', $appt->meeting_type)) }}</td>
            <td>{{ ucfirst($appt->status) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<p style="font-size: 8pt; color: #666; margin-top: 3px;">Total: {{ $student->appointments->count() }} appointments</p>
@endif

{{-- ── Counseling Sessions ── --}}
<h2>VII. Counseling Sessions Logged</h2>
@if($student->counselingSessions->isEmpty())
    <p class="empty">No counseling session records.</p>
@else
<table class="records-table">
    <thead>
        <tr>
            <th style="width: 18%;">Date</th>
            <th style="width: 30%;">Counselor</th>
            <th style="width: 25%;">Presenting Concern</th>
            <th style="width: 15%;">Status</th>
            <th style="width: 12%;">Follow-up</th>
        </tr>
    </thead>
    <tbody>
        @foreach($student->counselingSessions->sortByDesc('created_at') as $sess)
        <tr>
            <td>{{ $sess->created_at->format('M d, Y') }}</td>
            <td>{{ $sess->counselor?->name ?? '—' }}</td>
            <td>{{ ucwords(str_replace('_', ' ', $sess->presenting_concern ?? '—')) }}</td>
            <td>{{ ucwords(str_replace('_', ' ', $sess->session_status ?? '—')) }}</td>
            <td>{{ optional($sess->follow_up_date)->format('M d') ?? '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<p style="font-size: 8pt; color: #666; margin-top: 3px;">Total: {{ $student->counselingSessions->count() }} sessions. Case-note bodies are confidential and excluded from this record.</p>
@endif

{{-- ── Referrals ── --}}
<h2>VIII. Faculty Referrals</h2>
@if($student->referrals->isEmpty())
    <p class="empty">No referral records.</p>
@else
<table class="records-table">
    <thead>
        <tr>
            <th style="width: 14%;">Date</th>
            <th style="width: 22%;">Category</th>
            <th style="width: 12%;">Urgency</th>
            <th style="width: 24%;">Referred By</th>
            <th style="width: 14%;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($student->referrals->sortByDesc('created_at') as $ref)
        <tr>
            <td>{{ $ref->created_at->format('M d, Y') }}</td>
            <td>{{ ucwords(str_replace('_', ' ', $ref->reason_category)) }}</td>
            <td>{{ ucfirst($ref->urgency) }}</td>
            <td>{{ $ref->referredBy?->name ?? '—' }}</td>
            <td>{{ ucwords(str_replace('_', ' ', $ref->status)) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- ── Disciplinary ── --}}
<h2>IX. Disciplinary Records</h2>
@if($student->disciplinaryRecords->isEmpty())
    <p class="empty">No disciplinary records on file.</p>
@else
<table class="records-table">
    <thead>
        <tr>
            <th style="width: 14%;">Incident Date</th>
            <th style="width: 12%;">Type</th>
            <th style="width: 20%;">Category</th>
            <th style="width: 25%;">Description</th>
            <th style="width: 14%;">Status</th>
            <th>Sanction</th>
        </tr>
    </thead>
    <tbody>
        @foreach($student->disciplinaryRecords->sortByDesc('incident_date') as $disc)
        <tr>
            <td>{{ $disc->incident_date->format('M d, Y') }}</td>
            <td>{{ ucfirst($disc->offense_type) }}</td>
            <td>{{ $disc->offense_category }}</td>
            <td>{{ \Illuminate\Support\Str::limit($disc->description, 80) }}</td>
            <td>{{ ucwords(str_replace('_', ' ', $disc->status)) }}</td>
            <td>{{ $disc->sanction ?? '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- ── Test Results ── --}}
<h2>X. Psychological Test Results</h2>
@if($student->testResults->isEmpty())
    <p class="empty">No test results on file.</p>
@else
<table class="records-table">
    <thead>
        <tr>
            <th style="width: 14%;">Test Date</th>
            <th style="width: 35%;">Test</th>
            <th style="width: 18%;">Raw Score</th>
            <th style="width: 18%;">Interpretation</th>
            <th>Released?</th>
        </tr>
    </thead>
    <tbody>
        @foreach($student->testResults->sortByDesc('test_date') as $tr)
        <tr>
            <td>{{ $tr->test_date?->format('M d, Y') ?? '—' }}</td>
            <td>{{ $tr->test?->test_name ?? '—' }}</td>
            <td>{{ $tr->raw_score ?? '—' }}</td>
            <td>{{ $tr->interpretation_level ?? '—' }}</td>
            <td>{{ $tr->is_released ? 'Yes' : 'No' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- ── Certificates ── --}}
<h2>XI. Good Moral Certificates Issued</h2>
@if($student->certificates->isEmpty())
    <p class="empty">No certificates issued.</p>
@else
<table class="records-table">
    <thead>
        <tr>
            <th style="width: 18%;">Certificate No.</th>
            <th style="width: 14%;">Issued On</th>
            <th style="width: 38%;">Purpose</th>
            <th style="width: 12%;">Validity</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($student->certificates->sortByDesc('issued_at') as $cert)
        <tr>
            <td>{{ $cert->certificate_number }}</td>
            <td>{{ $cert->issued_at?->format('M d, Y') ?? '—' }}</td>
            <td>{{ $cert->purpose }}</td>
            <td>{{ $cert->validity_months }} months</td>
            <td>{{ $cert->is_revoked ? 'Revoked' : 'Active' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- ── Documents ── --}}
@if($student->documents->isNotEmpty())
<h2>XII. Documents on File</h2>
<table class="records-table">
    <thead>
        <tr>
            <th style="width: 14%;">Uploaded</th>
            <th style="width: 25%;">Type</th>
            <th>File Name</th>
        </tr>
    </thead>
    <tbody>
        @foreach($student->documents as $doc)
        <tr>
            <td>{{ $doc->created_at->format('M d, Y') }}</td>
            <td>{{ $doc->document_type }}</td>
            <td>{{ $doc->file_name }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- ── Signature ── --}}
<div class="sig-grid">
    <div class="sig-cell">
        <div class="sig-line">
            <div class="sig-name">{{ $student->assignedCounselor?->name ?? '_____________________' }}</div>
            <div class="sig-role">Assigned Guidance Counselor</div>
        </div>
    </div>
    <div class="sig-cell">
        <div class="sig-line">
            <div class="sig-name">_____________________</div>
            <div class="sig-role">Guidance Director</div>
        </div>
    </div>
</div>

<div class="footer">
    Confidential — for official use only. Generated by CHMSU Guidance Management System on {{ now()->format('F d, Y \a\t h:i A') }}.
    <br>
    This record is computer-generated and does not require a wet signature for internal use.
</div>

</body>
</html>
