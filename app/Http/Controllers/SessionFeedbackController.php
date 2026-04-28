<?php

namespace App\Http\Controllers;

use App\Models\CounselingSession;
use App\Models\SessionFeedback;
use Illuminate\Http\Request;

class SessionFeedbackController extends Controller
{
    /**
     * Staff overview — aggregate feedback metrics.
     */
    public function index(Request $request)
    {
        abort_unless($request->user()->isStaff(), 403);

        $query = SessionFeedback::with(['session.counselor', 'session.appointment', 'studentProfile'])
            ->latest();

        if ($request->filled('counselor_id')) {
            $query->whereHas('session', fn ($q) => $q->where('counselor_id', $request->counselor_id));
        }

        $feedback = $query->paginate(30)->withQueryString();

        // Aggregate stats — counselor-scoped if a counselor is logged in
        $statsQuery = SessionFeedback::query();
        if ($request->user()->isCounselor()) {
            $statsQuery->whereHas('session', fn ($q) => $q->where('counselor_id', $request->user()->id));
        } elseif ($request->filled('counselor_id')) {
            $statsQuery->whereHas('session', fn ($q) => $q->where('counselor_id', $request->counselor_id));
        }

        $allFeedback = $statsQuery->get();

        $stats = [
            'total'           => $allFeedback->count(),
            'avg_overall'     => round($allFeedback->avg('overall_rating') ?? 0, 1),
            'avg_helpful'     => round($allFeedback->avg('helpful_score') ?? 0, 1),
            'avg_listened'    => round($allFeedback->avg('listened_score') ?? 0, 1),
            'avg_comfort'     => round($allFeedback->avg('comfort_score') ?? 0, 1),
            'recommend_pct'   => $allFeedback->count() ? round($allFeedback->where('would_recommend', true)->count() / $allFeedback->count() * 100) : 0,
            'resolved_pct'    => $allFeedback->count() ? round($allFeedback->where('issue_resolved', true)->count() / $allFeedback->count() * 100) : 0,
        ];

        $counselors = \App\Models\User::where('role', 'guidance_counselor')->orderBy('name')->get();

        return view('session-feedback.index', compact('feedback', 'stats', 'counselors'));
    }

    /**
     * Student survey form (linked from a completed session).
     */
    public function create(Request $request, CounselingSession $session)
    {
        $user = $request->user();
        abort_unless($user->isStudent(), 403);

        $profile = $user->studentProfile;
        abort_unless($profile && $session->student_profile_id === $profile->id, 403);

        // Already submitted?
        if (SessionFeedback::where('counseling_session_id', $session->id)->exists()) {
            return redirect()->route('appointments.index')->with('success', 'You already submitted feedback for this session. Thank you!');
        }

        $session->load('counselor', 'appointment');
        return view('session-feedback.create', compact('session'));
    }

    public function store(Request $request, CounselingSession $session)
    {
        $user = $request->user();
        abort_unless($user->isStudent(), 403);

        $profile = $user->studentProfile;
        abort_unless($profile && $session->student_profile_id === $profile->id, 403);

        if (SessionFeedback::where('counseling_session_id', $session->id)->exists()) {
            return redirect()->route('appointments.index')->with('success', 'Feedback already submitted.');
        }

        $data = $request->validate([
            'overall_rating'      => 'required|integer|min:1|max:5',
            'helpful_score'       => 'required|integer|min:1|max:5',
            'listened_score'      => 'required|integer|min:1|max:5',
            'comfort_score'       => 'required|integer|min:1|max:5',
            'would_recommend'     => 'sometimes|boolean',
            'issue_resolved'      => 'sometimes|boolean',
            'what_worked'         => 'nullable|string|max:1500',
            'what_could_improve'  => 'nullable|string|max:1500',
        ]);

        $data['counseling_session_id'] = $session->id;
        $data['student_profile_id']    = $profile->id;
        $data['would_recommend']       = $request->boolean('would_recommend');
        $data['issue_resolved']        = $request->boolean('issue_resolved');

        SessionFeedback::create($data);

        return redirect()->route('appointments.index')
            ->with('success', 'Thank you for your feedback! It helps us improve our services.');
    }

    public function show(SessionFeedback $sessionFeedback)
    {
        abort_unless(auth()->user()->isStaff(), 403);
        $sessionFeedback->load(['session.counselor', 'session.appointment', 'studentProfile']);
        return view('session-feedback.show', ['feedback' => $sessionFeedback]);
    }
}
