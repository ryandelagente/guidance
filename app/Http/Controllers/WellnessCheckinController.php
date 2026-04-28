<?php

namespace App\Http\Controllers;

use App\Models\StudentProfile;
use App\Models\WellnessCheckin;
use Illuminate\Http\Request;

class WellnessCheckinController extends Controller
{
    /**
     * Student: view own history.
     * Staff: monitoring board (all check-ins, filterable).
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isStudent()) {
            $profile = $user->studentProfile;
            abort_unless($profile, 403, 'No student profile.');

            $checkins = WellnessCheckin::where('student_profile_id', $profile->id)
                ->latest('created_at')
                ->paginate(20);

            return view('wellness.student-index', compact('checkins', 'profile'));
        }

        // Staff monitoring view
        $query = WellnessCheckin::with('studentProfile.assignedCounselor', 'reviewer')
            ->latest('created_at');

        if ($request->filled('risk')) {
            // Apply risk filter at the collection level since it's a calculated attribute
        }
        if ($request->filled('wants_counselor')) {
            $query->where('wants_counselor', true);
        }
        if ($request->filled('unreviewed')) {
            $query->where('reviewed', false);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        $checkins = $query->paginate(30)->withQueryString();

        // Filter by risk if requested (post-fetch since it's calculated)
        if ($request->filled('risk')) {
            $checkins->setCollection(
                $checkins->getCollection()->filter(fn ($c) => $c->risk_level === $request->risk)->values()
            );
        }

        $stats = [
            'last_7_days'      => WellnessCheckin::where('created_at', '>=', now()->subDays(7))->count(),
            'wants_counselor'  => WellnessCheckin::where('wants_counselor', true)->where('reviewed', false)->count(),
            'high_risk'        => WellnessCheckin::where('created_at', '>=', now()->subDays(7))->get()->filter(fn($c) => $c->risk_level === 'high')->count(),
            'unreviewed'       => WellnessCheckin::where('reviewed', false)->count(),
        ];

        return view('wellness.staff-index', compact('checkins', 'stats'));
    }

    public function create(Request $request)
    {
        $profile = $request->user()->studentProfile;
        abort_unless($profile, 403, 'No student profile.');

        // Already submitted today?
        $today = WellnessCheckin::where('student_profile_id', $profile->id)
            ->whereDate('created_at', today())
            ->first();

        return view('wellness.create', compact('profile', 'today'));
    }

    public function store(Request $request)
    {
        $profile = $request->user()->studentProfile;
        abort_unless($profile, 403, 'No student profile.');

        $data = $request->validate([
            'mood'            => 'required|integer|min:1|max:5',
            'stress_level'    => 'required|integer|min:1|max:5',
            'sleep_quality'   => 'required|integer|min:1|max:5',
            'academic_stress' => 'required|integer|min:1|max:5',
            'notes'           => 'nullable|string|max:1000',
            'wants_counselor' => 'sometimes|boolean',
        ]);

        $data['student_profile_id'] = $profile->id;
        $data['wants_counselor']    = $request->boolean('wants_counselor');

        $checkin = WellnessCheckin::create($data);

        // ── Crisis escalation: very low mood + high stress + wants counselor → notify on-call ──
        $crisisIndicators = ($checkin->mood <= 2)
            && ($checkin->stress_level >= 4)
            && $checkin->wants_counselor;

        // Detect explicit self-harm / suicide language in notes
        $crisisLanguage = $checkin->notes && preg_match('/\b(kill\s+myself|end\s+it|suicid|self.?harm|hopeless|cant\s+(go|do)|cant\s+cope|hurt\s+myself|end\s+my\s+life|no\s+point)\b/i', $checkin->notes);

        if ($crisisIndicators || $crisisLanguage) {
            $this->triggerCrisisAlert($checkin, $profile, $crisisLanguage);
        }

        return redirect()->route('wellness.index')
            ->with('success', 'Thank you for checking in. Your guidance counselor cares about your well-being.');
    }

    /**
     * On critical wellness signal — notify the assigned counselor + director immediately.
     */
    private function triggerCrisisAlert(WellnessCheckin $checkin, \App\Models\StudentProfile $profile, bool $hasCrisisLanguage): void
    {
        $recipients = collect();

        if ($profile->assignedCounselor?->email) {
            $recipients->push($profile->assignedCounselor->email);
        }

        $directors = \App\Models\User::whereIn('role', ['guidance_director','super_admin'])
            ->where('is_active', true)
            ->pluck('email');
        $recipients = $recipients->merge($directors)->filter()->unique()->values();

        $body = "🚨 CRISIS ALERT — A student's wellness check-in indicates they may need immediate support.\n\n"
              . "Student: {$profile->full_name}\n"
              . "Student ID: " . ($profile->student_id_number ?? '—') . "\n"
              . "Submitted: " . $checkin->created_at->format('F d, Y h:i A') . "\n\n"
              . "Mood: " . \App\Models\WellnessCheckin::moodLabel($checkin->mood) . " ({$checkin->mood}/5)\n"
              . "Stress: " . \App\Models\WellnessCheckin::intensityLabel($checkin->stress_level) . " ({$checkin->stress_level}/5)\n"
              . "Wants counselor: " . ($checkin->wants_counselor ? 'YES' : 'no') . "\n";

        if ($hasCrisisLanguage) {
            $body .= "\n⚠️ Their note contains language consistent with crisis or self-harm indicators.\n";
        }

        if ($checkin->notes) {
            $body .= "\nNote from student: \"{$checkin->notes}\"\n";
        }

        $body .= "\nReview immediately: " . route('wellness.show', $checkin) . "\n\n"
              . "If you cannot reach the student, contact NCMH 1553 or escalate to the Guidance Office (034) 460-0511.";

        foreach ($recipients as $email) {
            try {
                \Illuminate\Support\Facades\Mail::raw($body, function ($m) use ($email, $profile) {
                    $m->to($email)->subject("🚨 CRISIS ALERT — {$profile->full_name} — IMMEDIATE REVIEW");
                });
            } catch (\Throwable $e) {
                // Mail failure shouldn't block the student's submission
            }
        }

        \App\Models\AuditLog::record(
            action: 'created',
            subject: $checkin,
            description: "Crisis alert triggered for {$profile->full_name} — {$recipients->count()} staff notified",
        );
    }

    public function show(WellnessCheckin $wellness)
    {
        $user = auth()->user();

        // Students may only view their own
        if ($user->isStudent()) {
            abort_unless($wellness->student_profile_id === $user->studentProfile?->id, 403);
        }

        $wellness->load('studentProfile.assignedCounselor', 'reviewer');
        return view('wellness.show', ['checkin' => $wellness]);
    }

    public function review(Request $request, WellnessCheckin $wellness)
    {
        abort_unless($request->user()->isStaff(), 403);

        $wellness->update([
            'reviewed'    => true,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Marked as reviewed.');
    }
}
