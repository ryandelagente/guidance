<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\CounselingSession;
use App\Models\Referral;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\Request;

class CaseloadController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Director/admin can view any counselor's caseload via ?counselor_id=
        $counselorId = ($user->isGuidanceDirector() || $user->isSuperAdmin())
            ? ($request->integer('counselor_id') ?: $user->id)
            : $user->id;

        $counselors = ($user->isGuidanceDirector() || $user->isSuperAdmin())
            ? User::where('role', 'guidance_counselor')->where('is_active', true)->orderBy('name')->get()
            : collect();

        $students = StudentProfile::where('assigned_counselor_id', $counselorId)
            ->orderBy('last_name')
            ->get();

        // Bulk fetch related data to avoid N+1
        $studentIds = $students->pluck('id');

        $lastAppointments = Appointment::whereIn('student_profile_id', $studentIds)
            ->where('counselor_id', $counselorId)
            ->where('status', 'completed')
            ->selectRaw('student_profile_id, MAX(appointment_date) as last_date')
            ->groupBy('student_profile_id')
            ->pluck('last_date', 'student_profile_id');

        $nextAppointments = Appointment::whereIn('student_profile_id', $studentIds)
            ->where('counselor_id', $counselorId)
            ->where('appointment_date', '>=', today())
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('appointment_date')
            ->get()
            ->groupBy('student_profile_id')
            ->map(fn ($list) => $list->first());

        $activeReferralCounts = Referral::whereIn('student_profile_id', $studentIds)
            ->where('assigned_counselor_id', $counselorId)
            ->whereNotIn('status', ['resolved', 'closed'])
            ->selectRaw('student_profile_id, count(*) as total')
            ->groupBy('student_profile_id')
            ->pluck('total', 'student_profile_id');

        $followUps = CounselingSession::whereIn('student_profile_id', $studentIds)
            ->where('counselor_id', $counselorId)
            ->whereNotNull('follow_up_date')
            ->where('follow_up_date', '>=', today())
            ->orderBy('follow_up_date')
            ->get()
            ->groupBy('student_profile_id')
            ->map(fn ($list) => $list->first());

        // Risk assessment
        $rows = $students->map(function ($s) use ($lastAppointments, $nextAppointments, $activeReferralCounts, $followUps) {
            $lastDate = $lastAppointments[$s->id] ?? null;
            $next     = $nextAppointments[$s->id] ?? null;
            $referrals = $activeReferralCounts[$s->id] ?? 0;
            $followUp = $followUps[$s->id] ?? null;

            // Compute simple risk score
            $risk = 'low';
            if ($s->academic_status === 'at_risk' || $referrals >= 2) {
                $risk = 'high';
            } elseif ($s->academic_status === 'probation' || $referrals === 1) {
                $risk = 'medium';
            }

            return [
                'student'       => $s,
                'last_contact'  => $lastDate,
                'next_appt'     => $next,
                'active_refs'   => $referrals,
                'follow_up'     => $followUp,
                'risk'          => $risk,
            ];
        });

        // Filter by risk if requested
        if ($request->filled('risk')) {
            $rows = $rows->where('risk', $request->risk)->values();
        }

        // Sort
        $sort = $request->get('sort', 'name');
        $rows = match ($sort) {
            'last_contact' => $rows->sortBy(fn ($r) => $r['last_contact'] ?? '0000-00-00')->values(),
            'risk'         => $rows->sortBy(fn ($r) => match ($r['risk']) { 'high' => 1, 'medium' => 2, default => 3 })->values(),
            'referrals'    => $rows->sortByDesc('active_refs')->values(),
            default        => $rows,
        };

        $stats = [
            'total'         => $students->count(),
            'high_risk'     => $rows->where('risk', 'high')->count(),
            'medium_risk'   => $rows->where('risk', 'medium')->count(),
            'no_contact_30' => $rows->filter(fn ($r) => !$r['last_contact'] || \Carbon\Carbon::parse($r['last_contact'])->diffInDays(now()) > 30)->count(),
            'pending_followups' => $rows->whereNotNull('follow_up')->count(),
        ];

        return view('caseload.index', compact('rows', 'stats', 'counselors', 'counselorId'));
    }
}
