<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ClearanceRequest;
use App\Models\CounselingSession;
use App\Models\DisciplinaryRecord;
use App\Models\GoodMoralCertificate;
use App\Models\Referral;
use App\Models\StudentProfile;
use App\Models\TestResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    private function buildStats(Request $request): array
    {
        $year  = $request->integer('year', now()->year);
        $month = $request->integer('month', 0); // 0 = whole year

        // ── Student Demographics ────────────────────────────────────────────
        $totalStudents = StudentProfile::count();

        $byCollege = StudentProfile::select('college', DB::raw('count(*) as total'))
            ->whereNotNull('college')
            ->groupBy('college')
            ->orderByDesc('total')
            ->get();

        $bySex = StudentProfile::select('sex', DB::raw('count(*) as total'))
            ->groupBy('sex')
            ->get();

        $byYearLevel = StudentProfile::select('year_level', DB::raw('count(*) as total'))
            ->groupBy('year_level')
            ->orderBy('year_level')
            ->get();

        $byAcademicStatus = StudentProfile::select('academic_status', DB::raw('count(*) as total'))
            ->groupBy('academic_status')
            ->get();

        // ── Appointments ────────────────────────────────────────────────────
        $apptQuery = Appointment::whereYear('appointment_date', $year);
        if ($month) $apptQuery->whereMonth('appointment_date', $month);

        $totalAppointments = $apptQuery->count();

        $apptByStatus = (clone $apptQuery)->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->get();

        $apptByType = (clone $apptQuery)->select('appointment_type', DB::raw('count(*) as total'))
            ->groupBy('appointment_type')->get();

        $apptByMonth = Appointment::select(
                DB::raw('MONTH(appointment_date) as month'),
                DB::raw('count(*) as total')
            )
            ->whereYear('appointment_date', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // ── Sessions / Case Notes ───────────────────────────────────────────
        $totalSessions = CounselingSession::whereYear('created_at', $year)->count();

        $sessionsByConcern = CounselingSession::select('presenting_concern', DB::raw('count(*) as total'))
            ->whereYear('created_at', $year)
            ->groupBy('presenting_concern')
            ->orderByDesc('total')
            ->get();

        // ── Referrals ───────────────────────────────────────────────────────
        $refQuery = Referral::whereYear('created_at', $year);
        if ($month) $refQuery->whereMonth('created_at', $month);

        $totalReferrals = $refQuery->count();

        $refByStatus = (clone $refQuery)->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->get();

        $refByCategory = (clone $refQuery)->select('reason_category', DB::raw('count(*) as total'))
            ->groupBy('reason_category')->orderByDesc('total')->get();

        $refByUrgency = (clone $refQuery)->select('urgency', DB::raw('count(*) as total'))
            ->groupBy('urgency')->get();

        // ── Disciplinary ────────────────────────────────────────────────────
        $discQuery = DisciplinaryRecord::whereYear('incident_date', $year);
        if ($month) $discQuery->whereMonth('incident_date', $month);

        $totalDisciplinary = $discQuery->count();

        $discByType = (clone $discQuery)->select('offense_type', DB::raw('count(*) as total'))
            ->groupBy('offense_type')->get();

        $discByStatus = (clone $discQuery)->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->get();

        // ── Psychological Testing ───────────────────────────────────────────
        $totalResults = TestResult::whereYear('test_date', $year)->count();

        $resultsByLevel = TestResult::select('interpretation_level', DB::raw('count(*) as total'))
            ->whereYear('test_date', $year)
            ->whereNotNull('interpretation_level')
            ->groupBy('interpretation_level')
            ->get();

        // ── Clearance & Certificates ────────────────────────────────────────
        $totalClearance     = ClearanceRequest::whereYear('created_at', $year)->count();
        $approvedClearance  = ClearanceRequest::whereYear('created_at', $year)->where('status', 'approved')->count();
        $totalCertificates  = GoodMoralCertificate::whereYear('issued_at', $year)->count();

        $clearanceByType = ClearanceRequest::select('clearance_type', DB::raw('count(*) as total'))
            ->whereYear('created_at', $year)
            ->groupBy('clearance_type')->get();

        return compact(
            'year', 'month',
            'totalStudents', 'byCollege', 'bySex', 'byYearLevel', 'byAcademicStatus',
            'totalAppointments', 'apptByStatus', 'apptByType', 'apptByMonth',
            'totalSessions', 'sessionsByConcern',
            'totalReferrals', 'refByStatus', 'refByCategory', 'refByUrgency',
            'totalDisciplinary', 'discByType', 'discByStatus',
            'totalResults', 'resultsByLevel',
            'totalClearance', 'approvedClearance', 'totalCertificates', 'clearanceByType'
        );
    }

    public function index(Request $request)
    {
        return view('analytics.dashboard', $this->buildStats($request));
    }

    // ── CSV Exports ─────────────────────────────────────────────────────────

    public function exportStudents()
    {
        $students = StudentProfile::with('assignedCounselor')->get();

        $headers = ['Student ID', 'Last Name', 'First Name', 'Middle Name', 'Sex', 'Year Level',
                    'College', 'Program', 'Student Type', 'Academic Status', 'Assigned Counselor'];

        return $this->csvDownload('students_' . now()->format('Ymd') . '.csv', $headers, $students->map(fn ($s) => [
            $s->student_id_number, $s->last_name, $s->first_name, $s->middle_name,
            $s->sex, $s->year_level, $s->college, $s->program,
            $s->student_type, $s->academic_status,
            $s->assignedCounselor?->name ?? '',
        ]));
    }

    public function exportReferrals(Request $request)
    {
        $year = $request->integer('year', now()->year);
        $referrals = Referral::with(['studentProfile', 'referredBy', 'assignedCounselor'])
            ->whereYear('created_at', $year)->get();

        $headers = ['Date', 'Student', 'Student ID', 'Category', 'Urgency', 'Status',
                    'Referred By', 'Assigned Counselor', 'Acknowledged', 'Resolved'];

        return $this->csvDownload("referrals_{$year}.csv", $headers, $referrals->map(fn ($r) => [
            $r->created_at->format('Y-m-d'),
            $r->studentProfile?->full_name ?? '',
            $r->studentProfile?->student_id_number ?? '',
            $r->reason_category, $r->urgency, $r->status,
            $r->referredBy?->name ?? '',
            $r->assignedCounselor?->name ?? '',
            $r->acknowledged_at?->format('Y-m-d') ?? '',
            $r->resolved_at?->format('Y-m-d') ?? '',
        ]));
    }

    public function exportAppointments(Request $request)
    {
        $year = $request->integer('year', now()->year);
        $appts = Appointment::with(['studentProfile', 'counselor'])
            ->whereYear('appointment_date', $year)->get();

        $headers = ['Date', 'Time', 'Student', 'Student ID', 'Counselor', 'Type', 'Mode', 'Status'];

        return $this->csvDownload("appointments_{$year}.csv", $headers, $appts->map(fn ($a) => [
            $a->appointment_date->format('Y-m-d'),
            substr($a->start_time, 0, 5),
            $a->studentProfile?->full_name ?? '',
            $a->studentProfile?->student_id_number ?? '',
            $a->counselor?->name ?? '',
            $a->appointment_type, $a->meeting_type, $a->status,
        ]));
    }

    public function exportDisciplinary(Request $request)
    {
        $year = $request->integer('year', now()->year);
        $records = DisciplinaryRecord::with(['studentProfile', 'reportedBy', 'handledBy'])
            ->whereYear('incident_date', $year)->get();

        $headers = ['Incident Date', 'Student', 'Student ID', 'Type', 'Category',
                    'Status', 'Sanction', 'Reported By', 'Handled By'];

        return $this->csvDownload("disciplinary_{$year}.csv", $headers, $records->map(fn ($d) => [
            $d->incident_date->format('Y-m-d'),
            $d->studentProfile?->full_name ?? '',
            $d->studentProfile?->student_id_number ?? '',
            $d->offense_type, $d->offense_category, $d->status,
            $d->sanction ?? '',
            $d->reportedBy?->name ?? '',
            $d->handledBy?->name ?? '',
        ]));
    }

    public function report(Request $request)
    {
        return view('analytics.report', $this->buildStats($request));
    }

    private function csvDownload(string $filename, array $headers, $rows): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $out = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $headers);
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
