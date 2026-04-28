<?php

namespace App\Http\Controllers;

use App\Models\ActionPlan;
use App\Models\ActionPlanMilestone;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\Request;

class ActionPlanController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = ActionPlan::with(['studentProfile', 'counselor', 'milestones'])
            ->orderByRaw("FIELD(status, 'active','draft','on_hold','completed','cancelled')")
            ->latest();

        if ($user->isCounselor()) {
            $query->where('counselor_id', $user->id);
        } elseif ($user->isStudent()) {
            $profile = $user->studentProfile;
            abort_unless($profile, 403);
            $query->where('student_profile_id', $profile->id)
                  ->whereIn('status', ['active', 'completed']);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('focus_area')) {
            $query->where('focus_area', $request->focus_area);
        }

        $plans = $query->paginate(20)->withQueryString();

        return view('action-plans.index', compact('plans'));
    }

    public function create(Request $request)
    {
        $this->authorizeStaff();

        $students = StudentProfile::orderBy('last_name')->get();
        $selectedStudent = $request->filled('student_profile_id')
            ? StudentProfile::find($request->student_profile_id)
            : null;

        return view('action-plans.create', compact('students', 'selectedStudent'));
    }

    public function store(Request $request)
    {
        $this->authorizeStaff();

        $data = $request->validate([
            'student_profile_id' => 'required|exists:student_profiles,id',
            'title'              => 'required|string|max:200',
            'description'        => 'nullable|string|max:3000',
            'focus_area'         => 'required|in:' . implode(',', array_keys(ActionPlan::FOCUS_AREAS)),
            'status'             => 'required|in:' . implode(',', array_keys(ActionPlan::STATUSES)),
            'start_date'         => 'required|date',
            'target_date'        => 'nullable|date|after_or_equal:start_date',
            'milestones'         => 'nullable|array',
            'milestones.*.description' => 'required|string|max:300',
            'milestones.*.target_date' => 'nullable|date',
        ]);

        $milestones = $data['milestones'] ?? [];
        unset($data['milestones']);

        $data['counselor_id'] = $request->user()->id;
        $plan = ActionPlan::create($data);

        foreach ($milestones as $i => $m) {
            $plan->milestones()->create([
                'description' => $m['description'],
                'target_date' => $m['target_date'] ?? null,
                'sort_order'  => $i,
            ]);
        }

        return redirect()->route('action-plans.show', $plan)->with('success', 'Action plan created.');
    }

    public function show(ActionPlan $actionPlan)
    {
        $user = auth()->user();
        if ($user->isStudent()) {
            abort_unless($actionPlan->student_profile_id === $user->studentProfile?->id, 403);
        }

        $actionPlan->load(['studentProfile.assignedCounselor', 'counselor', 'milestones']);
        return view('action-plans.show', ['plan' => $actionPlan]);
    }

    public function edit(ActionPlan $actionPlan)
    {
        $this->authorizeStaff();
        $actionPlan->load('milestones', 'studentProfile');
        return view('action-plans.edit', ['plan' => $actionPlan]);
    }

    public function update(Request $request, ActionPlan $actionPlan)
    {
        $this->authorizeStaff();

        $data = $request->validate([
            'title'         => 'required|string|max:200',
            'description'   => 'nullable|string|max:3000',
            'focus_area'    => 'required|in:' . implode(',', array_keys(ActionPlan::FOCUS_AREAS)),
            'status'        => 'required|in:' . implode(',', array_keys(ActionPlan::STATUSES)),
            'start_date'    => 'required|date',
            'target_date'   => 'nullable|date|after_or_equal:start_date',
            'outcome_notes' => 'nullable|string|max:3000',
        ]);

        if ($data['status'] === 'completed' && !$actionPlan->completed_at) {
            $data['completed_at'] = now();
        }

        $actionPlan->update($data);
        return redirect()->route('action-plans.show', $actionPlan)->with('success', 'Action plan updated.');
    }

    public function destroy(ActionPlan $actionPlan)
    {
        $this->authorizeStaff();
        $actionPlan->delete();
        return redirect()->route('action-plans.index')->with('success', 'Action plan deleted.');
    }

    // Milestones
    public function addMilestone(Request $request, ActionPlan $actionPlan)
    {
        $this->authorizeStaff();
        $data = $request->validate([
            'description' => 'required|string|max:300',
            'target_date' => 'nullable|date',
        ]);
        $data['sort_order'] = $actionPlan->milestones()->max('sort_order') + 1;
        $actionPlan->milestones()->create($data);

        return back()->with('success', 'Milestone added.');
    }

    public function toggleMilestone(Request $request, ActionPlan $actionPlan, ActionPlanMilestone $milestone)
    {
        $this->authorizeStaff();
        abort_unless($milestone->action_plan_id === $actionPlan->id, 404);

        $milestone->update([
            'completed_at' => $milestone->completed_at ? null : now(),
        ]);

        return back();
    }

    public function deleteMilestone(ActionPlan $actionPlan, ActionPlanMilestone $milestone)
    {
        $this->authorizeStaff();
        abort_unless($milestone->action_plan_id === $actionPlan->id, 404);
        $milestone->delete();
        return back()->with('success', 'Milestone removed.');
    }

    private function authorizeStaff(): void
    {
        abort_unless(auth()->user()->isStaff(), 403);
    }
}
