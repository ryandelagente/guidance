<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;

class CaseNotePinController extends Controller
{
    public function setup()
    {
        $user = auth()->user();
        abort_unless($user->isStaff(), 403);
        return view('case-note-pin.setup', [
            'isResetting' => $user->hasCaseNotePin(),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        abort_unless($user->isStaff(), 403);

        $request->validate([
            'pin'              => 'required|digits_between:4,6|same:pin_confirmation',
            'pin_confirmation' => 'required',
            'password'         => 'required|current_password',
        ], [
            'pin.digits_between' => 'PIN must be 4 to 6 digits.',
            'pin.same'           => 'PIN confirmation does not match.',
            'password.current_password' => 'Your account password is incorrect.',
        ]);

        $user->setCaseNotePin($request->pin);

        // Mark this session as verified so the user can immediately access notes
        session(['case_note_pin_verified_at' => now()->timestamp]);

        AuditLog::record(
            action: 'updated',
            subject: $user,
            description: 'Case-note PIN set/changed',
        );

        $intended = session('intended', route('sessions.index'));
        session()->forget('intended');

        return redirect($intended)->with('success', 'Case-note PIN saved. You can now view confidential case notes.');
    }

    public function verify()
    {
        $user = auth()->user();
        abort_unless($user->isStaff(), 403);

        if (!$user->hasCaseNotePin()) {
            return redirect()->route('case-note-pin.setup');
        }

        return view('case-note-pin.verify');
    }

    public function check(Request $request)
    {
        $user = $request->user();
        abort_unless($user->isStaff(), 403);

        // Rate-limit to prevent brute force
        $key = 'case-note-pin:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'pin' => "Too many failed attempts. Try again in {$seconds} seconds.",
            ]);
        }

        $request->validate(['pin' => 'required|digits_between:4,6']);

        if (!$user->verifyCaseNotePin($request->pin)) {
            RateLimiter::hit($key, 60);

            AuditLog::record(
                action: 'failed_login',
                subject: $user,
                description: 'Incorrect case-note PIN attempt',
            );

            throw ValidationException::withMessages([
                'pin' => 'Incorrect PIN. ' . (5 - RateLimiter::attempts($key)) . ' attempts remaining.',
            ]);
        }

        RateLimiter::clear($key);
        session(['case_note_pin_verified_at' => now()->timestamp]);

        AuditLog::record(
            action: 'viewed',
            subject: $user,
            description: 'Case-note PIN verified',
        );

        $intended = session('intended', route('sessions.index'));
        session()->forget('intended');

        return redirect($intended);
    }

    public function lock(Request $request)
    {
        session()->forget('case_note_pin_verified_at');
        return redirect()->route('sessions.index')
            ->with('success', 'Case notes locked. You will need to re-enter your PIN to view them.');
    }
}
