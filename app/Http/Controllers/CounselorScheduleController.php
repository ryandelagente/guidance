<?php

namespace App\Http\Controllers;

use App\Models\CounselorSchedule;
use App\Models\User;
use Illuminate\Http\Request;

class CounselorScheduleController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isCounselor()) {
            $counselor = $user;
        } else {
            $counselor = User::find($request->counselor_id) ?? $user;
        }

        $schedules  = CounselorSchedule::where('counselor_id', $counselor->id)
                        ->orderByRaw("FIELD(day_of_week,'monday','tuesday','wednesday','thursday','friday','saturday')")
                        ->get();
        $counselors = User::where('role', 'guidance_counselor')->where('is_active', true)->get();

        return view('schedules.index', compact('schedules', 'counselor', 'counselors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'counselor_id'  => 'required|exists:users,id',
            'day_of_week'   => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'required|date_format:H:i|after:start_time',
            'slot_duration' => 'required|integer|in:30,45,60,90',
        ]);

        // One schedule row per counselor per day
        CounselorSchedule::updateOrCreate(
            ['counselor_id' => $data['counselor_id'], 'day_of_week' => $data['day_of_week']],
            $data + ['is_active' => true]
        );

        return back()->with('success', 'Schedule saved.');
    }

    public function update(Request $request, CounselorSchedule $schedule)
    {
        $data = $request->validate([
            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'required|date_format:H:i|after:start_time',
            'slot_duration' => 'required|integer|in:30,45,60,90',
            'is_active'     => 'boolean',
        ]);

        $schedule->update($data);
        return back()->with('success', 'Schedule updated.');
    }

    public function destroy(CounselorSchedule $schedule)
    {
        $schedule->delete();
        return back()->with('success', 'Schedule removed.');
    }

    // Not used — no individual show/edit pages needed
    public function create() {}
    public function show(string $id) {}
    public function edit(string $id) {}
}
