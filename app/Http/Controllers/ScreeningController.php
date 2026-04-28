<?php

namespace App\Http\Controllers;

use App\Models\ScreeningResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ScreeningController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isStudent()) {
            $profile = $user->studentProfile;
            abort_unless($profile, 403);

            $responses = ScreeningResponse::where('student_profile_id', $profile->id)
                ->latest()->get();

            return view('screening.student-index', compact('responses'));
        }

        // Staff view
        $query = ScreeningResponse::with('studentProfile', 'reviewer')
            ->orderByRaw("FIELD(severity, 'severe','moderately_severe','moderate','mild','minimal','low')")
            ->latest();

        if ($request->filled('instrument')) {
            $query->where('instrument', $request->instrument);
        }
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }
        if ($request->filled('unreviewed')) {
            $query->where('reviewed', false);
        }

        $responses = $query->paginate(30)->withQueryString();

        $stats = [
            'severe'         => ScreeningResponse::whereIn('severity', ['severe','moderately_severe'])->count(),
            'self_harm_flag' => ScreeningResponse::where('positive_self_harm', true)->where('reviewed', false)->count(),
            'unreviewed'     => ScreeningResponse::where('reviewed', false)->count(),
            'last_30_days'   => ScreeningResponse::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        return view('screening.staff-index', compact('responses', 'stats'));
    }

    public function start(string $instrument, Request $request)
    {
        abort_unless(in_array($instrument, ['phq9','gad7','k10']), 404);
        abort_unless($request->user()->isStudent(), 403);
        abort_unless($request->user()->studentProfile, 403);

        $questions = ScreeningResponse::questions($instrument);
        $options   = ScreeningResponse::options($instrument);
        $label     = ScreeningResponse::INSTRUMENTS[$instrument];

        return view('screening.take', compact('instrument', 'questions', 'options', 'label'));
    }

    public function store(string $instrument, Request $request)
    {
        abort_unless(in_array($instrument, ['phq9','gad7','k10']), 404);
        $user = $request->user();
        abort_unless($user->isStudent(), 403);
        $profile = $user->studentProfile;
        abort_unless($profile, 403);

        $request->validate([
            'answers'   => 'required|array',
            'answers.*' => 'required|integer',
        ]);

        $answers = $request->input('answers');
        $score   = array_sum(array_map('intval', $answers));
        $severity = ScreeningResponse::interpretScore($instrument, $score);

        // PHQ-9 Q9 ("thoughts of being better off dead or hurting yourself")
        $selfHarmFlag = $instrument === 'phq9' && (int) ($answers[8] ?? 0) > 0;

        $response = ScreeningResponse::create([
            'student_profile_id' => $profile->id,
            'instrument'         => $instrument,
            'answers'            => $answers,
            'total_score'        => $score,
            'severity'           => $severity,
            'positive_self_harm' => $selfHarmFlag,
        ]);

        // Auto-alert staff on critical results
        if (in_array($severity, ['severe','moderately_severe']) || $selfHarmFlag) {
            $this->alertStaff($response, $profile, $selfHarmFlag);
        }

        return redirect()->route('screening.show', $response)
            ->with('success', 'Your screening was submitted. Results below.');
    }

    public function show(ScreeningResponse $screening, Request $request)
    {
        $user = $request->user();
        if ($user->isStudent()) {
            abort_unless($screening->student_profile_id === $user->studentProfile?->id, 403);
        } else {
            abort_unless($user->isStaff(), 403);
        }

        $screening->load('studentProfile', 'reviewer');
        $questions = ScreeningResponse::questions($screening->instrument);
        $options   = ScreeningResponse::options($screening->instrument);

        return view('screening.show', compact('screening', 'questions', 'options'));
    }

    public function review(Request $request, ScreeningResponse $screening)
    {
        abort_unless($request->user()->isStaff(), 403);

        $request->validate(['counselor_notes' => 'nullable|string|max:2000']);

        $screening->update([
            'reviewed'        => true,
            'reviewed_by'     => $request->user()->id,
            'reviewed_at'     => now(),
            'counselor_notes' => $request->counselor_notes,
        ]);

        return back()->with('success', 'Screening reviewed.');
    }

    private function alertStaff(ScreeningResponse $screening, \App\Models\StudentProfile $profile, bool $selfHarmFlag): void
    {
        $recipients = collect();
        if ($profile->assignedCounselor?->email) {
            $recipients->push($profile->assignedCounselor->email);
        }
        $directors = User::whereIn('role', ['guidance_director','super_admin'])
            ->where('is_active', true)
            ->pluck('email');
        $recipients = $recipients->merge($directors)->filter()->unique();

        $instrumentName = ScreeningResponse::INSTRUMENTS[$screening->instrument];
        $subject = $selfHarmFlag
            ? "🚨 SCREENING ALERT — Self-harm indicator from {$profile->full_name}"
            : "⚠️ Severe screening result — {$profile->full_name}";

        $body = "Student: {$profile->full_name} ({$profile->student_id_number})\n"
              . "Instrument: {$instrumentName}\n"
              . "Score: {$screening->total_score}\n"
              . "Severity: " . ucwords(str_replace('_', ' ', $screening->severity)) . "\n";

        if ($selfHarmFlag) {
            $body .= "\n⚠️ The student answered POSITIVELY to PHQ-9 Q9 (thoughts of being better off dead or hurting themselves). This requires immediate clinical follow-up.\n";
        }

        $body .= "\nReview: " . route('screening.show', $screening) . "\n\nIf you can't reach the student promptly, escalate to NCMH 1553 or call them directly.";

        foreach ($recipients as $email) {
            try {
                Mail::raw($body, fn ($m) => $m->to($email)->subject($subject));
            } catch (\Throwable $e) {
                // continue
            }
        }

        \App\Models\AuditLog::record(
            action: 'created',
            subject: $screening,
            description: "Screening alert sent — {$instrumentName} severe / self-harm flag for {$profile->full_name}",
        );
    }
}
