<?php

namespace App\Http\Controllers;

use App\Models\StudentProfile;
use App\Models\User;
use App\Models\WalkInQueue;
use Illuminate\Http\Request;

class WalkInQueueController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();

        // Today's queue, prioritized: crisis → urgent → normal, then by arrival
        $waiting = WalkInQueue::with('studentProfile', 'counselor')
            ->where('status', 'waiting')
            ->whereDate('arrived_at', $today)
            ->orderByRaw("FIELD(priority, 'crisis', 'urgent', 'normal')")
            ->orderBy('arrived_at')
            ->get();

        $beingSeen = WalkInQueue::with('studentProfile', 'counselor')
            ->where('status', 'being_seen')
            ->whereDate('arrived_at', $today)
            ->orderBy('called_at')
            ->get();

        $completed = WalkInQueue::with('studentProfile', 'counselor')
            ->whereIn('status', ['completed', 'no_show', 'cancelled'])
            ->whereDate('arrived_at', $today)
            ->orderByDesc('completed_at')
            ->get();

        $stats = [
            'total_today'   => WalkInQueue::whereDate('arrived_at', $today)->count(),
            'waiting'       => $waiting->count(),
            'being_seen'    => $beingSeen->count(),
            'completed'     => $completed->where('status', 'completed')->count(),
            'avg_wait'      => $completed->where('status', 'completed')->isEmpty()
                ? 0
                : round($completed->where('status', 'completed')->avg(fn ($w) => $w->arrived_at->diffInMinutes($w->called_at ?? $w->completed_at))),
        ];

        $counselors = User::where('role', 'guidance_counselor')->where('is_active', true)->orderBy('name')->get();
        $students = StudentProfile::orderBy('last_name')->limit(500)->get(['id', 'first_name', 'last_name', 'student_id_number']);

        return view('walk-in-queue.index', compact('waiting', 'beingSeen', 'completed', 'stats', 'counselors', 'students'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_profile_id' => 'nullable|exists:student_profiles,id',
            'name'               => 'required_without:student_profile_id|nullable|string|max:200',
            'contact_number'     => 'nullable|string|max:50',
            'reason'             => 'required|string|max:200',
            'priority'           => 'required|in:normal,urgent,crisis',
            'assigned_counselor_id' => 'nullable|exists:users,id',
            'notes'              => 'nullable|string|max:500',
        ]);

        $data['arrived_at'] = now();
        $data['status']     = 'waiting';

        WalkInQueue::create($data);

        return back()->with('success', 'Added to walk-in queue.');
    }

    public function call(WalkInQueue $walkInQueue, Request $request)
    {
        abort_unless($request->user()->isStaff(), 403);

        $walkInQueue->update([
            'status'                => 'being_seen',
            'called_at'             => now(),
            'assigned_counselor_id' => $walkInQueue->assigned_counselor_id ?? $request->user()->id,
        ]);

        return back()->with('success', 'Marked as being seen.');
    }

    public function complete(WalkInQueue $walkInQueue, Request $request)
    {
        abort_unless($request->user()->isStaff(), 403);

        $request->validate(['outcome_notes' => 'nullable|string|max:500']);

        $walkInQueue->update([
            'status'       => 'completed',
            'completed_at' => now(),
            'notes'        => $request->outcome_notes ?? $walkInQueue->notes,
        ]);

        return back()->with('success', 'Walk-in completed.');
    }

    public function noShow(WalkInQueue $walkInQueue, Request $request)
    {
        abort_unless($request->user()->isStaff(), 403);

        $walkInQueue->update([
            'status'       => 'no_show',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Marked as no-show.');
    }

    public function destroy(WalkInQueue $walkInQueue, Request $request)
    {
        abort_unless($request->user()->isStaff(), 403);

        $walkInQueue->update([
            'status'       => 'cancelled',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Cancelled.');
    }
}
