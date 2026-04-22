<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\CounselingSession;
use Illuminate\Http\Request;

class CounselingSessionController extends Controller
{
    public function index(Request $request)
    {
        $sessions = CounselingSession::with(['studentProfile', 'counselor', 'appointment'])
            ->where('counselor_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('sessions.index', compact('sessions'));
    }

    // Open case notes for a specific appointment
    public function create(Request $request)
    {
        $request->validate(['appointment_id' => 'required|exists:appointments,id']);
        $appointment = Appointment::with(['studentProfile', 'counselor'])->findOrFail($request->appointment_id);

        // Ensure only the assigned counselor or admin can write case notes
        $user = $request->user();
        abort_unless($user->isSuperAdmin() || $user->id === $appointment->counselor_id, 403);

        $session = $appointment->session;

        return view('sessions.create', compact('appointment', 'session'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'appointment_id'    => 'required|exists:appointments,id',
            'case_notes'        => 'nullable|string',
            'recommendations'   => 'nullable|string|max:2000',
            'follow_up_date'    => 'nullable|date|after:today',
            'session_status'    => 'required|in:initial,ongoing,terminated,referred',
            'presenting_concern'=> 'nullable|in:academic,personal_social,career,financial,family,mental_health,behavioral,other',
        ]);

        $appointment = Appointment::findOrFail($data['appointment_id']);
        $user        = $request->user();
        abort_unless($user->isSuperAdmin() || $user->id === $appointment->counselor_id, 403);

        CounselingSession::updateOrCreate(
            ['appointment_id' => $data['appointment_id']],
            array_merge($data, [
                'counselor_id'       => $appointment->counselor_id,
                'student_profile_id' => $appointment->student_profile_id,
            ])
        );

        // Mark appointment as completed when notes are saved
        $appointment->update(['status' => 'completed']);

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Case notes saved and encrypted successfully.');
    }

    public function show(CounselingSession $session)
    {
        $user = request()->user();
        abort_unless($user->isSuperAdmin() || $user->id === $session->counselor_id, 403);
        $session->load(['studentProfile', 'counselor', 'appointment']);
        return view('sessions.show', compact('session'));
    }

    public function edit(CounselingSession $session)
    {
        $user = request()->user();
        abort_unless($user->isSuperAdmin() || $user->id === $session->counselor_id, 403);
        $session->load(['appointment.studentProfile']);
        return view('sessions.edit', compact('session'));
    }

    public function update(Request $request, CounselingSession $session)
    {
        abort_unless($request->user()->isSuperAdmin() || $request->user()->id === $session->counselor_id, 403);

        $data = $request->validate([
            'case_notes'         => 'nullable|string',
            'recommendations'    => 'nullable|string|max:2000',
            'follow_up_date'     => 'nullable|date',
            'session_status'     => 'required|in:initial,ongoing,terminated,referred',
            'presenting_concern' => 'nullable|in:academic,personal_social,career,financial,family,mental_health,behavioral,other',
        ]);

        $session->update($data);

        return redirect()->route('sessions.show', $session)
            ->with('success', 'Case notes updated.');
    }

    public function destroy(CounselingSession $session)
    {
        abort_unless(request()->user()->isSuperAdmin(), 403);
        $session->delete();
        return back()->with('success', 'Session record deleted.');
    }
}
