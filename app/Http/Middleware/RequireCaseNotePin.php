<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireCaseNotePin
{
    /**
     * Gate: counselors must verify their PIN before viewing case notes.
     * Verification lasts for the configured TTL (default 15 min) per session.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->isStaff()) {
            return $next($request);
        }

        // Not yet set → redirect to PIN setup
        if (!$user->hasCaseNotePin()) {
            return redirect()->route('case-note-pin.setup')
                ->with('intended', $request->fullUrl());
        }

        $verifiedAt = session('case_note_pin_verified_at');
        $ttl = 15 * 60; // 15 minutes

        if (!$verifiedAt || (now()->timestamp - $verifiedAt) > $ttl) {
            return redirect()->route('case-note-pin.verify')
                ->with('intended', $request->fullUrl());
        }

        return $next($request);
    }
}
