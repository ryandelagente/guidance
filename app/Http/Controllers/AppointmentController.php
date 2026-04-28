<?php

namespace App\Http\Controllers;

use App\Mail\AppointmentBooked;
use App\Mail\AppointmentConfirmed;
use App\Models\Appointment;
use App\Models\CounselorSchedule;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $user  = $request->user();
        $query = Appointment::with(['studentProfile', 'counselor', 'session'])
                            ->orderBy('appointment_date')
                            ->orderBy('start_time');

        if ($user->isStudent()) {
            $query->forStudent($user->studentProfile->id ?? 0);
        } elseif ($user->isCounselor()) {
            $query->forCounselor($user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        $appointments = $query->paginate(20)->withQueryString();

        // Calendar data
        try {
            $calendarMonth = $request->filled('month')
                ? Carbon::createFromFormat('Y-m', $request->month)->startOfMonth()
                : Carbon::now()->startOfMonth();
        } catch (\Exception $e) {
            $calendarMonth = Carbon::now()->startOfMonth();
        }

        $calQuery = Appointment::with(['studentProfile'])
            ->whereBetween('appointment_date', [
                $calendarMonth->copy()->startOfMonth()->toDateString(),
                $calendarMonth->copy()->endOfMonth()->toDateString(),
            ]);

        if ($user->isStudent()) {
            $calQuery->forStudent($user->studentProfile->id ?? 0);
        } elseif ($user->isCounselor()) {
            $calQuery->forCounselor($user->id);
        }

        $calendarAppointments = $calQuery->get()
            ->groupBy(fn ($a) => $a->appointment_date->format('Y-m-d'));

        return view('appointments.index', compact('appointments', 'calendarMonth', 'calendarAppointments'));
    }

    // Student booking form
    public function create(Request $request)
    {
        $counselors = User::where('role', 'guidance_counselor')
                         ->where('is_active', true)
                         ->get();

        $selectedCounselor = null;
        $availableSlots    = [];

        if ($request->filled('counselor_id') && $request->filled('date')) {
            $selectedCounselor = User::find($request->counselor_id);
            $availableSlots    = $this->getAvailableSlots(
                $request->counselor_id,
                $request->date
            );
        }

        return view('appointments.create', compact('counselors', 'selectedCounselor', 'availableSlots'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'counselor_id'       => 'required|exists:users,id',
            'appointment_type'   => 'required|in:academic,personal_social,career,crisis',
            'appointment_date'   => 'required|date|after_or_equal:today',
            'start_time'         => 'required',
            'meeting_type'       => 'required|in:in_person,virtual',
            'student_concern'    => 'nullable|string|max:1000',
        ]);

        $user = $request->user();

        // Counselors/admins can book on behalf of a student
        if ($user->isStudent()) {
            $studentProfile = $user->studentProfile;
            abort_unless($studentProfile, 403, 'No student profile found.');
        } else {
            $request->validate(['student_profile_id' => 'required|exists:student_profiles,id']);
            $studentProfile = StudentProfile::findOrFail($request->student_profile_id);
        }

        // Derive end_time from the counselor's slot duration
        $schedule = CounselorSchedule::where('counselor_id', $data['counselor_id'])
            ->where('day_of_week', strtolower(Carbon::parse($data['appointment_date'])->format('l')))
            ->where('is_active', true)
            ->first();

        $duration = $schedule?->slot_duration ?? 60;
        $endTime  = Carbon::createFromTimeString($data['start_time'])->addMinutes($duration)->format('H:i');

        // Check no double-booking
        $conflict = Appointment::where('counselor_id', $data['counselor_id'])
            ->where('appointment_date', $data['appointment_date'])
            ->where('start_time', $data['start_time'])
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->exists();

        if ($conflict) {
            return back()->withErrors(['start_time' => 'This time slot is already booked. Please choose another.'])->withInput();
        }

        // Auto-generate a Jitsi Meet room for virtual appointments
        $meetingLink = null;
        if ($data['meeting_type'] === 'virtual') {
            $room = 'CHMSU-Guidance-' . strtolower(\Illuminate\Support\Str::random(10));
            $meetingLink = "https://meet.jit.si/{$room}";
        }

        $appointment = Appointment::create([
            'student_profile_id' => $studentProfile->id,
            'counselor_id'       => $data['counselor_id'],
            'appointment_type'   => $data['appointment_type'],
            'appointment_date'   => $data['appointment_date'],
            'start_time'         => $data['start_time'],
            'end_time'           => $endTime,
            'meeting_type'       => $data['meeting_type'],
            'meeting_link'       => $meetingLink,
            'student_concern'    => $data['student_concern'] ?? null,
            'status'             => 'pending',
        ]);

        // Notify the counselor
        $appointment->load(['studentProfile', 'counselor']);
        if ($appointment->counselor?->email) {
            Mail::to($appointment->counselor->email)->queue(new AppointmentBooked($appointment));
        }

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Appointment request submitted. You will be notified once confirmed.');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['studentProfile', 'counselor', 'session', 'cancelledBy']);
        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        return view('appointments.edit', compact('appointment'));
    }

    // Counselor/admin status update
    public function update(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'status'             => 'required|in:pending,confirmed,in_progress,completed,cancelled,no_show',
            'notes_for_student'  => 'nullable|string|max:2000',
            'meeting_link'       => 'nullable|url|max:500',
            'cancelled_reason'   => 'required_if:status,cancelled|nullable|string|max:500',
        ]);

        if ($data['status'] === 'cancelled') {
            $data['cancelled_by'] = $request->user()->id;
        }

        $previousStatus = $appointment->status;
        $appointment->update($data);

        // Notify the student when the counselor confirms
        if ($data['status'] === 'confirmed' && $previousStatus !== 'confirmed') {
            $appointment->load(['studentProfile.user', 'counselor']);
            $studentEmail = $appointment->studentProfile?->user?->email;
            if ($studentEmail) {
                Mail::to($studentEmail)->queue(new AppointmentConfirmed($appointment));
            }
        }

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Appointment updated successfully.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->update(['status' => 'cancelled', 'cancelled_by' => request()->user()->id]);
        return redirect()->route('appointments.index')->with('success', 'Appointment cancelled.');
    }

    // AJAX endpoint: return available time slots for a counselor on a date
    public function slots(Request $request)
    {
        $request->validate([
            'counselor_id' => 'required|exists:users,id',
            'date'         => 'required|date',
        ]);

        $slots = $this->getAvailableSlots($request->counselor_id, $request->date);

        return response()->json($slots);
    }

    private function getAvailableSlots(int $counselorId, string $date): array
    {
        $dayName  = strtolower(Carbon::parse($date)->format('l'));
        $schedule = CounselorSchedule::where('counselor_id', $counselorId)
            ->where('day_of_week', $dayName)
            ->where('is_active', true)
            ->first();

        if (!$schedule) return [];

        $allSlots = $schedule->generateSlots();

        // Remove already-booked slots
        $booked = Appointment::where('counselor_id', $counselorId)
            ->where('appointment_date', $date)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->pluck('start_time')
            ->map(fn ($t) => substr($t, 0, 5))
            ->toArray();

        return array_values(array_filter($allSlots, fn ($s) => !in_array($s, $booked)));
    }
}
