<?php

namespace App\Http\Controllers;

use App\Models\RiasecQuestion;
use App\Models\RiasecResponse;
use App\Models\StudentProfile;
use Illuminate\Http\Request;

class RiasecController extends Controller
{
    /**
     * Student lands here. Shows past results + button to take a new test.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isStudent()) {
            $profile = $user->studentProfile;
            abort_unless($profile, 403);
            $responses = RiasecResponse::where('student_profile_id', $profile->id)
                ->latest('completed_at')->get();
            return view('riasec.student-index', compact('responses', 'profile'));
        }

        // Staff: see all responses
        $query = RiasecResponse::with('studentProfile')->latest('completed_at');
        if ($request->filled('top_code')) {
            $query->where('top_code', 'like', $request->top_code . '%');
        }
        $responses = $query->paginate(30)->withQueryString();

        return view('riasec.staff-index', compact('responses'));
    }

    /**
     * Test form.
     */
    public function create(Request $request)
    {
        $user = $request->user();
        abort_unless($user->isStudent(), 403);
        abort_unless($user->studentProfile, 403);

        $questions = RiasecQuestion::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($questions->isEmpty()) {
            return redirect()->route('riasec.index')
                ->with('error', 'The Career Interest Inventory has not been set up yet. Contact your guidance counselor.');
        }

        return view('riasec.create', compact('questions'));
    }

    /**
     * Submit answers.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        abort_unless($user->isStudent(), 403);
        $profile = $user->studentProfile;
        abort_unless($profile, 403);

        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'in:0,1',
        ]);

        $answers = $request->input('answers'); // [question_id => 0|1]

        // Tally by type
        $scores = ['R' => 0, 'I' => 0, 'A' => 0, 'S' => 0, 'E' => 0, 'C' => 0];

        $questions = RiasecQuestion::whereIn('id', array_keys($answers))->get()->keyBy('id');
        foreach ($answers as $qid => $ans) {
            if ((int) $ans === 1 && isset($questions[$qid])) {
                $scores[$questions[$qid]->type]++;
            }
        }

        // Compute top 3 codes
        arsort($scores);
        $topCode = implode('', array_slice(array_keys($scores), 0, 3));

        $response = RiasecResponse::create([
            'student_profile_id' => $profile->id,
            'score_r' => $scores['R'],
            'score_i' => $scores['I'],
            'score_a' => $scores['A'],
            'score_s' => $scores['S'],
            'score_e' => $scores['E'],
            'score_c' => $scores['C'],
            'top_code'     => $topCode,
            'answers'      => $answers,
            'completed_at' => now(),
        ]);

        return redirect()->route('riasec.show', $response)
            ->with('success', 'Your career interest profile is ready!');
    }

    /**
     * Show a single result.
     */
    public function show(RiasecResponse $riasec)
    {
        $user = auth()->user();
        if ($user->isStudent()) {
            abort_unless($riasec->student_profile_id === $user->studentProfile?->id, 403);
        }
        $riasec->load('studentProfile');
        return view('riasec.show', ['response' => $riasec]);
    }
}
