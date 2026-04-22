<?php

namespace App\Http\Controllers;

use App\Mail\ReferralSubmitted;
use App\Models\Referral;
use App\Models\ReferralIntervention;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ReferralController extends Controller
{
    public function index(Request $request)
    {
        $user  = $request->user();
        $query = Referral::with(['studentProfile', 'referredBy', 'assignedCounselor', 'interventions'])
                         ->latest();

        // Faculty only see their own referrals
        if ($user->isFaculty()) {
            $query->where('referred_by', $user->id);
        }

        // Counselor sees referrals assigned to them + unassigned
        if ($user->isCounselor()) {
            $query->where(fn ($q) =>
                $q->where('assigned_counselor_id', $user->id)
                  ->orWhereNull('assigned_counselor_id')
            );
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('urgency')) {
            $query->where('urgency', $request->urgency);
        }

        $referrals = $query->paginate(20)->withQueryString();

        return view('referrals.index', compact('referrals'));
    }

    // Faculty submission form
    public function create()
    {
        $students   = StudentProfile::orderBy('last_name')->get();
        $counselors = User::where('role', 'guidance_counselor')->where('is_active', true)->get();
        return view('referrals.create', compact('students', 'counselors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_profile_id'    => 'required|exists:student_profiles,id',
            'assigned_counselor_id' => 'nullable|exists:users,id',
            'reason_category'       => 'required|in:academic,behavioral,attendance,personal,mental_health,financial,other',
            'urgency'               => 'required|in:low,medium,high,critical',
            'description'           => 'required|string|min:20|max:3000',
        ]);

        $referral = Referral::create(array_merge($data, [
            'referred_by' => $request->user()->id,
            'status'      => 'pending',
        ]));

        $referral->load(['studentProfile', 'referredBy']);

        // Email the assigned counselor, or all active counselors if unassigned
        $recipients = $data['assigned_counselor_id']
            ? User::where('id', $data['assigned_counselor_id'])->pluck('email')
            : User::where('role', 'guidance_counselor')->where('is_active', true)->pluck('email');

        foreach ($recipients as $email) {
            Mail::to($email)->queue(new ReferralSubmitted($referral));
        }

        return redirect()->route('referrals.show', $referral)
            ->with('success', 'Referral submitted. The guidance office will take action shortly.');
    }

    public function show(Referral $referral)
    {
        $user = request()->user();

        // Faculty can only view their own referrals
        if ($user->isFaculty()) {
            abort_unless($referral->referred_by === $user->id, 403);
        }

        $referral->load(['studentProfile', 'referredBy', 'assignedCounselor', 'interventions.counselor']);

        $counselors = $user->isStaff()
            ? User::where('role', 'guidance_counselor')->where('is_active', true)->get()
            : collect();

        return view('referrals.show', compact('referral', 'counselors'));
    }

    public function edit(Referral $referral)
    {
        // Faculty can edit only pending referrals they submitted
        $user = request()->user();
        if ($user->isFaculty()) {
            abort_unless($referral->referred_by === $user->id && $referral->status === 'pending', 403);
        }

        $students   = StudentProfile::orderBy('last_name')->get();
        $counselors = User::where('role', 'guidance_counselor')->where('is_active', true)->get();
        return view('referrals.edit', compact('referral', 'students', 'counselors'));
    }

    public function update(Request $request, Referral $referral)
    {
        $user = $request->user();

        if ($user->isFaculty()) {
            // Faculty can only edit description/urgency on pending referrals
            abort_unless($referral->referred_by === $user->id && $referral->status === 'pending', 403);
            $data = $request->validate([
                'urgency'     => 'required|in:low,medium,high,critical',
                'description' => 'required|string|min:20|max:3000',
            ]);
            $referral->update($data);
        } else {
            // Counselor/admin: assign counselor + provide faculty feedback
            $data = $request->validate([
                'assigned_counselor_id' => 'nullable|exists:users,id',
                'faculty_feedback'      => 'nullable|string|max:1000',
            ]);
            $referral->update($data);
        }

        return redirect()->route('referrals.show', $referral)->with('success', 'Referral updated.');
    }

    // Counselor adds an intervention log entry
    public function addIntervention(Request $request, Referral $referral)
    {
        $data = $request->validate([
            'status_label'   => 'required|string|max:100',
            'new_status'     => 'required|in:pending,acknowledged,in_progress,resolved,closed',
            'internal_notes' => 'nullable|string|max:2000',
            'faculty_feedback' => 'nullable|string|max:1000',
        ]);

        ReferralIntervention::create([
            'referral_id'    => $referral->id,
            'counselor_id'   => $request->user()->id,
            'status_label'   => $data['status_label'],
            'new_status'     => $data['new_status'],
            'internal_notes' => $data['internal_notes'] ?? null,
        ]);

        $updates = ['status' => $data['new_status']];

        if ($data['new_status'] === 'acknowledged' && !$referral->acknowledged_at) {
            $updates['acknowledged_at'] = now();
        }
        if (in_array($data['new_status'], ['resolved', 'closed'])) {
            $updates['resolved_at'] = now();
        }
        if (!empty($data['faculty_feedback'])) {
            $updates['faculty_feedback'] = $data['faculty_feedback'];
        }

        $referral->update($updates);

        return back()->with('success', 'Intervention logged and referral status updated.');
    }

    public function destroy(Referral $referral)
    {
        abort_unless(request()->user()->isSuperAdmin(), 403);
        $referral->delete();
        return redirect()->route('referrals.index')->with('success', 'Referral deleted.');
    }
}
