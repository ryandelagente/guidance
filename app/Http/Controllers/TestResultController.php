<?php

namespace App\Http\Controllers;

use App\Models\PsychologicalTest;
use App\Models\StudentProfile;
use App\Models\TestResult;
use App\Models\TestSchedule;
use Illuminate\Http\Request;

class TestResultController extends Controller
{
    public function index(Request $request)
    {
        $user  = $request->user();
        $query = TestResult::with(['studentProfile', 'test', 'recordedBy'])
                           ->orderBy('test_date', 'desc');

        // Students only see their own released results
        if ($user->isStudent()) {
            $profile = $user->studentProfile;
            abort_unless($profile, 403);
            $query->where('student_profile_id', $profile->id)
                  ->where('is_released', true);
        }

        if ($request->filled('search') && !$user->isStudent()) {
            $s = $request->search;
            $query->whereHas('studentProfile', fn ($q) =>
                $q->where('first_name', 'like', "%$s%")
                  ->orWhere('last_name', 'like', "%$s%")
                  ->orWhere('student_id_number', 'like', "%$s%")
            );
        }
        if ($request->filled('test_id')) {
            $query->where('psychological_test_id', $request->test_id);
        }

        $results = $query->paginate(20)->withQueryString();
        $tests   = $user->isStudent()
            ? collect()
            : PsychologicalTest::where('is_active', true)->orderBy('name')->get();

        return view('test-results.index', compact('results', 'tests'));
    }

    public function create(Request $request)
    {
        $students  = StudentProfile::orderBy('last_name')->get();
        $tests     = PsychologicalTest::where('is_active', true)->orderBy('name')->get();
        $schedules = TestSchedule::with('test')->orderBy('scheduled_date', 'desc')->get();
        $selectedSchedule = $request->filled('schedule_id')
            ? TestSchedule::find($request->schedule_id)
            : null;
        return view('test-results.create', compact('students', 'tests', 'schedules', 'selectedSchedule'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_profile_id'    => 'required|exists:student_profiles,id',
            'psychological_test_id' => 'required|exists:psychological_tests,id',
            'test_schedule_id'      => 'nullable|exists:test_schedules,id',
            'raw_score'             => 'nullable|integer|min:0',
            'percentile'            => 'nullable|numeric|min:0|max:100',
            'grade_equivalent'      => 'nullable|string|max:20',
            'interpretation_level'  => 'nullable|in:very_low,low,average,above_average,superior,very_superior',
            'interpretation'        => 'nullable|string|max:3000',
            'career_matches'        => 'nullable|string',
            'test_date'             => 'required|date|before_or_equal:today',
            'is_released'           => 'boolean',
        ]);

        // Parse comma-separated careers into JSON array
        if (!empty($data['career_matches'])) {
            $data['career_matches'] = array_filter(
                array_map('trim', explode(',', $data['career_matches']))
            );
        }

        $data['recorded_by'] = $request->user()->id;

        TestResult::create($data);

        $redirect = $data['test_schedule_id']
            ? route('test-schedules.show', $data['test_schedule_id'])
            : route('test-results.index');

        return redirect($redirect)->with('success', 'Test result recorded.');
    }

    public function show(TestResult $testResult)
    {
        $user = request()->user();

        // Students may only view released results for themselves
        if ($user->isStudent()) {
            $profile = $user->studentProfile;
            abort_unless(
                $testResult->is_released && $profile && $testResult->student_profile_id === $profile->id,
                403
            );
        }

        $testResult->load(['studentProfile', 'test', 'schedule', 'recordedBy']);
        return view('test-results.show', compact('testResult'));
    }

    public function edit(TestResult $testResult)
    {
        $students  = StudentProfile::orderBy('last_name')->get();
        $tests     = PsychologicalTest::where('is_active', true)->orderBy('name')->get();
        $schedules = TestSchedule::with('test')->orderBy('scheduled_date', 'desc')->get();
        return view('test-results.edit', compact('testResult', 'students', 'tests', 'schedules'));
    }

    public function update(Request $request, TestResult $testResult)
    {
        $data = $request->validate([
            'raw_score'            => 'nullable|integer|min:0',
            'percentile'           => 'nullable|numeric|min:0|max:100',
            'grade_equivalent'     => 'nullable|string|max:20',
            'interpretation_level' => 'nullable|in:very_low,low,average,above_average,superior,very_superior',
            'interpretation'       => 'nullable|string|max:3000',
            'career_matches'       => 'nullable|string',
            'test_date'            => 'required|date|before_or_equal:today',
            'is_released'          => 'boolean',
        ]);

        if (!empty($data['career_matches'])) {
            $data['career_matches'] = array_filter(
                array_map('trim', explode(',', $data['career_matches']))
            );
        } else {
            $data['career_matches'] = null;
        }

        $testResult->update($data);

        return redirect()->route('test-results.show', $testResult)->with('success', 'Result updated.');
    }

    public function destroy(TestResult $testResult)
    {
        abort_unless(request()->user()->isSuperAdmin(), 403);
        $testResult->delete();
        return redirect()->route('test-results.index')->with('success', 'Result deleted.');
    }
}
