<?php

namespace App\Http\Controllers;

use App\Models\PsychologicalTest;
use App\Models\TestSchedule;
use App\Models\User;
use Illuminate\Http\Request;

class TestScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = TestSchedule::with(['test', 'administeredBy'])
                             ->orderBy('scheduled_date', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $schedules = $query->paginate(20)->withQueryString();
        return view('test-schedules.index', compact('schedules'));
    }

    public function create()
    {
        $tests      = PsychologicalTest::where('is_active', true)->orderBy('name')->get();
        $counselors = User::where('role', 'guidance_counselor')->where('is_active', true)->get();
        return view('test-schedules.create', compact('tests', 'counselors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'psychological_test_id'  => 'required|exists:psychological_tests,id',
            'administered_by'        => 'required|exists:users,id',
            'college'                => 'nullable|string|max:200',
            'program'                => 'nullable|string|max:200',
            'year_level'             => 'nullable|in:1st,2nd,3rd,4th,5th,Graduate',
            'scheduled_date'         => 'required|date',
            'start_time'             => 'required',
            'venue'                  => 'nullable|string|max:200',
            'expected_participants'  => 'nullable|integer|min:1',
            'notes'                  => 'nullable|string|max:1000',
        ]);

        TestSchedule::create($data);

        return redirect()->route('test-schedules.index')
            ->with('success', 'Testing session scheduled.');
    }

    public function show(TestSchedule $testSchedule)
    {
        $testSchedule->load(['test', 'administeredBy', 'results.studentProfile']);
        return view('test-schedules.show', compact('testSchedule'));
    }

    public function edit(TestSchedule $testSchedule)
    {
        $tests      = PsychologicalTest::where('is_active', true)->orderBy('name')->get();
        $counselors = User::where('role', 'guidance_counselor')->where('is_active', true)->get();
        return view('test-schedules.edit', compact('testSchedule', 'tests', 'counselors'));
    }

    public function update(Request $request, TestSchedule $testSchedule)
    {
        $data = $request->validate([
            'administered_by'       => 'required|exists:users,id',
            'college'               => 'nullable|string|max:200',
            'program'               => 'nullable|string|max:200',
            'year_level'            => 'nullable|in:1st,2nd,3rd,4th,5th,Graduate',
            'scheduled_date'        => 'required|date',
            'start_time'            => 'required',
            'venue'                 => 'nullable|string|max:200',
            'expected_participants' => 'nullable|integer|min:1',
            'notes'                 => 'nullable|string|max:1000',
            'status'                => 'required|in:scheduled,ongoing,completed,cancelled',
        ]);

        $testSchedule->update($data);

        return redirect()->route('test-schedules.show', $testSchedule)
            ->with('success', 'Schedule updated.');
    }

    public function destroy(TestSchedule $testSchedule)
    {
        abort_unless(request()->user()->isSuperAdmin(), 403);
        $testSchedule->delete();
        return redirect()->route('test-schedules.index')->with('success', 'Schedule deleted.');
    }
}
