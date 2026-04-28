<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\CounselingSession;
use App\Models\Referral;
use App\Models\SessionFeedback;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        // Director, super admin, and counselors viewing their own data
        $user = $request->user();
        abort_unless($user->isStaff(), 403);

        $year  = $request->integer('year', now()->year);
        $month = $request->integer('month', 0);

        $counselors = User::where('role', 'guidance_counselor')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Per-counselor metrics
        $rows = $counselors->map(function ($c) use ($year, $month) {
            $apptScope = function ($q) use ($year, $month) {
                $q->whereYear('appointment_date', $year);
                if ($month) $q->whereMonth('appointment_date', $month);
            };
            $sessionScope = function ($q) use ($year, $month) {
                $q->whereYear('created_at', $year);
                if ($month) $q->whereMonth('created_at', $month);
            };
            $refScope = function ($q) use ($year, $month) {
                $q->whereYear('created_at', $year);
                if ($month) $q->whereMonth('created_at', $month);
            };

            $totalAppts = Appointment::where('counselor_id', $c->id)->where($apptScope)->count();
            $completedAppts = Appointment::where('counselor_id', $c->id)->where('status', 'completed')->where($apptScope)->count();
            $noShowAppts = Appointment::where('counselor_id', $c->id)->where('status', 'no_show')->where($apptScope)->count();

            $sessionsLogged = CounselingSession::where('counselor_id', $c->id)->where($sessionScope)->count();

            $referralsHandled = Referral::where('assigned_counselor_id', $c->id)->where($refScope)->count();
            $referralsResolved = Referral::where('assigned_counselor_id', $c->id)
                ->whereIn('status', ['resolved','closed'])->where($refScope)->count();

            // Avg response time on referrals (created → acknowledged) in hours
            $avgResponseHours = Referral::where('assigned_counselor_id', $c->id)
                ->whereNotNull('acknowledged_at')
                ->where($refScope)
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, acknowledged_at)) as avg_hrs')
                ->value('avg_hrs');

            // Feedback aggregates
            $feedback = SessionFeedback::whereHas('session', fn ($q) => $q->where('counselor_id', $c->id))
                ->whereYear('created_at', $year)
                ->when($month, fn ($q) => $q->whereMonth('created_at', $month))
                ->get();

            $avgRating = $feedback->avg('overall_rating');
            $recommendPct = $feedback->count() ? round($feedback->where('would_recommend', true)->count() / $feedback->count() * 100) : null;

            // Active caseload (not time-bound)
            $activeStudents = StudentProfile::where('assigned_counselor_id', $c->id)->count();

            return [
                'counselor'         => $c,
                'active_students'   => $activeStudents,
                'total_appts'       => $totalAppts,
                'completed_appts'   => $completedAppts,
                'completion_rate'   => $totalAppts ? round($completedAppts / $totalAppts * 100) : 0,
                'no_show_rate'      => $totalAppts ? round($noShowAppts / $totalAppts * 100) : 0,
                'sessions_logged'   => $sessionsLogged,
                'referrals_handled' => $referralsHandled,
                'referrals_resolved' => $referralsResolved,
                'resolution_rate'   => $referralsHandled ? round($referralsResolved / $referralsHandled * 100) : 0,
                'avg_response_hrs'  => $avgResponseHours ? round($avgResponseHours, 1) : null,
                'feedback_count'    => $feedback->count(),
                'avg_rating'        => $avgRating ? round($avgRating, 1) : null,
                'recommend_pct'     => $recommendPct,
            ];
        });

        // Office-wide totals
        $totals = [
            'team_size'         => $counselors->count(),
            'total_appts'       => $rows->sum('total_appts'),
            'completed_appts'   => $rows->sum('completed_appts'),
            'sessions_logged'   => $rows->sum('sessions_logged'),
            'referrals_handled' => $rows->sum('referrals_handled'),
            'avg_rating'        => $rows->whereNotNull('avg_rating')->avg('avg_rating'),
        ];
        $totals['avg_rating'] = $totals['avg_rating'] ? round($totals['avg_rating'], 1) : null;

        return view('performance.index', compact('rows', 'totals', 'counselors', 'year', 'month'));
    }
}
