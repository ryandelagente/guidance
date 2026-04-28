<?php

namespace App\Http\Controllers;

use App\Models\GroupSession;
use App\Models\GroupSessionParticipant;
use App\Models\StudentProfile;
use Illuminate\Http\Request;

class GroupSessionController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->isStaff(), 403);

        $query = GroupSession::with('counselor', 'participants')
            ->orderByDesc('session_date');

        if ($request->filled('focus')) $query->where('focus', $request->focus);
        if ($request->filled('status')) $query->where('status', $request->status);

        $sessions = $query->paginate(20)->withQueryString();
        return view('group-sessions.index', compact('sessions'));
    }

    public function create()
    {
        abort_unless(auth()->user()->isStaff(), 403);
        $students = StudentProfile::orderBy('last_name')->get(['id','first_name','last_name','student_id_number']);
        return view('group-sessions.create', compact('students'));
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->isStaff(), 403);

        $data = $request->validate([
            'title'            => 'required|string|max:200',
            'description'      => 'nullable|string|max:3000',
            'focus'            => 'required|in:' . implode(',', array_keys(GroupSession::FOCUSES)),
            'session_date'     => 'required|date',
            'start_time'       => 'required',
            'end_time'         => 'required|after:start_time',
            'venue'            => 'required|string|max:200',
            'max_participants' => 'required|integer|min:2|max:100',
            'status'           => 'required|in:scheduled,in_progress,completed,cancelled',
            'participants'     => 'array',
            'participants.*'   => 'exists:student_profiles,id',
        ]);

        $participants = $data['participants'] ?? [];
        unset($data['participants']);

        $data['counselor_id'] = $request->user()->id;
        $session = GroupSession::create($data);

        foreach ($participants as $studentId) {
            GroupSessionParticipant::create([
                'group_session_id'   => $session->id,
                'student_profile_id' => $studentId,
                'attendance'         => 'registered',
            ]);
        }

        return redirect()->route('group-sessions.show', $session)->with('success', 'Group session created.');
    }

    public function show(GroupSession $groupSession)
    {
        abort_unless(auth()->user()->isStaff(), 403);
        $groupSession->load('counselor', 'participants.studentProfile');
        return view('group-sessions.show', ['session' => $groupSession]);
    }

    public function addParticipant(GroupSession $groupSession, Request $request)
    {
        abort_unless($request->user()->isStaff(), 403);

        $request->validate(['student_profile_id' => 'required|exists:student_profiles,id']);

        if ($groupSession->isFull()) {
            return back()->withErrors(['student_profile_id' => 'Group session is at full capacity.']);
        }

        GroupSessionParticipant::firstOrCreate([
            'group_session_id'   => $groupSession->id,
            'student_profile_id' => $request->student_profile_id,
        ], ['attendance' => 'registered']);

        return back()->with('success', 'Participant added.');
    }

    public function updateAttendance(GroupSession $groupSession, GroupSessionParticipant $participant, Request $request)
    {
        abort_unless($request->user()->isStaff(), 403);
        abort_unless($participant->group_session_id === $groupSession->id, 404);

        $request->validate(['attendance' => 'required|in:registered,attended,no_show,withdrew']);
        $participant->update(['attendance' => $request->attendance]);

        return back();
    }

    public function update(Request $request, GroupSession $groupSession)
    {
        abort_unless($request->user()->isStaff(), 403);

        $data = $request->validate([
            'status'      => 'required|in:scheduled,in_progress,completed,cancelled',
            'group_notes' => 'nullable|string|max:5000',
        ]);

        $groupSession->update($data);
        return back()->with('success', 'Session updated.');
    }

    public function destroy(GroupSession $groupSession)
    {
        abort_unless(auth()->user()->isStaff(), 403);
        $groupSession->delete();
        return redirect()->route('group-sessions.index')->with('success', 'Group session deleted.');
    }
}
