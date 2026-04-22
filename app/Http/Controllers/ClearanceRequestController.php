<?php

namespace App\Http\Controllers;

use App\Mail\ClearanceStatusUpdated;
use App\Models\ClearanceRequest;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ClearanceRequestController extends Controller
{
    public function index(Request $request)
    {
        $user  = $request->user();
        $query = ClearanceRequest::with(['studentProfile', 'processedBy'])->latest();

        // Students only see their own
        if ($user->isStudent()) {
            $profile = $user->studentProfile;
            abort_unless($profile, 403);
            $query->where('student_profile_id', $profile->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('clearance_type', $request->type);
        }

        $requests = $query->paginate(20)->withQueryString();
        return view('clearance.index', compact('requests'));
    }

    public function create()
    {
        $user    = request()->user();
        $profile = $user->isStudent() ? $user->studentProfile : null;
        $students = $user->isStaff() ? StudentProfile::orderBy('last_name')->get() : collect();
        return view('clearance.create', compact('profile', 'students'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'student_profile_id' => 'required|exists:student_profiles,id',
            'clearance_type'     => 'required|in:graduation,departmental,scholarship,employment,other',
            'academic_year'      => 'required|string|max:20',
            'semester'           => 'required|in:1st,2nd,Summer',
            'purpose'            => 'nullable|string|max:500',
        ]);

        // If student submits, enforce own profile
        if ($user->isStudent()) {
            $data['student_profile_id'] = $user->studentProfile->id;
        }

        $status = $data['clearance_type'] === 'graduation' ? 'for_exit_survey' : 'pending';

        $clearance = ClearanceRequest::create(array_merge($data, ['status' => $status]));

        if ($status === 'for_exit_survey') {
            return redirect()->route('exit-survey.show', $clearance)
                ->with('info', 'Please complete the exit survey to proceed with your graduation clearance.');
        }

        return redirect()->route('clearance.show', $clearance)
            ->with('success', 'Clearance request submitted.');
    }

    public function show(ClearanceRequest $clearance)
    {
        $user = request()->user();
        if ($user->isStudent()) {
            abort_unless($clearance->student_profile_id === $user->studentProfile?->id, 403);
        }
        $clearance->load(['studentProfile', 'processedBy', 'certificate', 'surveyResponses.question']);
        return view('clearance.show', compact('clearance'));
    }

    public function update(Request $request, ClearanceRequest $clearance)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,for_exit_survey,survey_done,approved,rejected,on_hold',
            'notes'  => 'nullable|string|max:1000',
        ]);

        $data['processed_by'] = $request->user()->id;
        $data['processed_at'] = now();

        $clearance->update($data);
        $clearance->load(['studentProfile.user']);

        // Notify the student
        $studentEmail = $clearance->studentProfile?->user?->email;
        if ($studentEmail) {
            Mail::to($studentEmail)->queue(new ClearanceStatusUpdated($clearance));
        }

        return redirect()->route('clearance.show', $clearance)->with('success', 'Clearance status updated.');
    }

    public function destroy(ClearanceRequest $clearance)
    {
        abort_unless(request()->user()->isSuperAdmin(), 403);
        $clearance->delete();
        return redirect()->route('clearance.index')->with('success', 'Request deleted.');
    }
}
