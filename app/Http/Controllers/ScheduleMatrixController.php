<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\CounselorSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ScheduleMatrixController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->isStaff(), 403);

        // Week to display (defaults to current week starting Monday)
        $weekStart = $request->filled('week')
            ? Carbon::parse($request->week)->startOfWeek(Carbon::MONDAY)
            : now()->startOfWeek(Carbon::MONDAY);

        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);
        $days    = collect(range(0, 6))->map(fn ($i) => $weekStart->copy()->addDays($i));

        $counselors = User::where('role', 'guidance_counselor')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Schedules: counselor_id => day_of_week => CounselorSchedule
        $schedules = CounselorSchedule::where('is_active', true)
            ->whereIn('counselor_id', $counselors->pluck('id'))
            ->get()
            ->groupBy('counselor_id')
            ->map(fn ($s) => $s->keyBy('day_of_week'));

        // Booked appointments this week — counselor_id => date => count
        $booked = Appointment::whereBetween('appointment_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->whereIn('counselor_id', $counselors->pluck('id'))
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->get(['id','counselor_id','appointment_date','start_time','status'])
            ->groupBy('counselor_id')
            ->map(fn ($items) => $items->groupBy(fn ($a) => $a->appointment_date->format('Y-m-d')));

        // Build matrix: rows = counselor, columns = day
        $matrix = $counselors->map(function ($c) use ($days, $schedules, $booked) {
            $row = ['counselor' => $c, 'cells' => []];
            foreach ($days as $day) {
                $dayKey   = strtolower($day->format('l'));
                $schedule = $schedules[$c->id][$dayKey] ?? null;
                $bookedHere = $booked[$c->id][$day->format('Y-m-d')] ?? collect();

                $row['cells'][] = [
                    'date'         => $day,
                    'schedule'     => $schedule,
                    'booked_count' => $bookedHere->count(),
                    'total_slots'  => $schedule ? count($schedule->generateSlots()) : 0,
                    'is_today'     => $day->isToday(),
                    'is_past'      => $day->isPast() && !$day->isToday(),
                ];
            }
            return $row;
        });

        $totals = [
            'available_slots' => $matrix->sum(fn ($r) => collect($r['cells'])->sum('total_slots')),
            'booked_slots'    => $matrix->sum(fn ($r) => collect($r['cells'])->sum('booked_count')),
        ];
        $totals['utilization'] = $totals['available_slots']
            ? round($totals['booked_slots'] / $totals['available_slots'] * 100)
            : 0;

        return view('schedule-matrix.index', compact('matrix', 'days', 'weekStart', 'weekEnd', 'totals'));
    }
}
