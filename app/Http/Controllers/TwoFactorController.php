<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Services\Totp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class TwoFactorController extends Controller
{
    /**
     * Settings page — show 2FA status, enable/disable buttons.
     */
    public function show(Request $request)
    {
        return view('two-factor.show', [
            'user'         => $request->user(),
            'pendingSetup' => session()->has('2fa_setup_secret'),
        ]);
    }

    /**
     * Setup page — generate a new secret and show the QR.
     */
    public function setup(Request $request)
    {
        $user = $request->user();

        if ($user->hasTwoFactorEnabled()) {
            return redirect()->route('two-factor.show')
                ->with('error', '2FA is already enabled. Disable it first to set up a new device.');
        }

        $secret = session('2fa_setup_secret');
        if (!$secret) {
            $secret = Totp::generateSecret();
            session(['2fa_setup_secret' => $secret]);
        }

        $uri = Totp::provisioningUri($secret, $user->email);

        return view('two-factor.setup', compact('secret', 'uri'));
    }

    /**
     * Verify the first code, generate recovery codes, enable 2FA.
     */
    public function enable(Request $request)
    {
        $user = $request->user();

        if ($user->hasTwoFactorEnabled()) {
            return redirect()->route('two-factor.show');
        }

        $secret = session('2fa_setup_secret');
        if (!$secret) {
            return redirect()->route('two-factor.setup');
        }

        $request->validate([
            'code' => 'required|digits:6',
        ]);

        if (!Totp::verify($secret, $request->code)) {
            throw ValidationException::withMessages([
                'code' => 'That code is incorrect. Make sure your phone\'s clock is correct and try again.',
            ]);
        }

        $recoveryCodes = $user->generateTwoFactorRecoveryCodes();

        $user->forceFill([
            'two_factor_secret'         => $secret,
            'two_factor_recovery_codes' => $recoveryCodes,
            'two_factor_enabled_at'     => now(),
        ])->save();

        session()->forget('2fa_setup_secret');
        session(['2fa_passed_at' => now()->timestamp]);

        AuditLog::record(
            action: 'updated',
            subject: $user,
            description: 'Two-factor authentication enabled',
        );

        return redirect()->route('two-factor.show')
            ->with('recoveryCodes', $recoveryCodes)
            ->with('success', '2FA enabled successfully. Save your recovery codes in a safe place.');
    }

    /**
     * Disable 2FA (requires current password).
     */
    public function disable(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user->forceFill([
            'two_factor_secret'         => null,
            'two_factor_recovery_codes' => null,
            'two_factor_enabled_at'     => null,
        ])->save();

        session()->forget(['2fa_passed_at', '2fa_setup_secret']);

        AuditLog::record(
            action: 'updated',
            subject: $user,
            description: 'Two-factor authentication disabled',
        );

        return redirect()->route('two-factor.show')
            ->with('success', 'Two-factor authentication disabled.');
    }

    /**
     * Regenerate recovery codes.
     */
    public function regenerateCodes(Request $request)
    {
        $user = $request->user();
        abort_unless($user->hasTwoFactorEnabled(), 422);

        $request->validate(['password' => 'required|current_password']);

        $codes = $user->generateTwoFactorRecoveryCodes();
        $user->forceFill(['two_factor_recovery_codes' => $codes])->save();

        AuditLog::record(
            action: 'updated',
            subject: $user,
            description: 'Two-factor recovery codes regenerated',
        );

        return redirect()->route('two-factor.show')
            ->with('recoveryCodes', $codes)
            ->with('success', 'New recovery codes generated. Old codes are no longer valid.');
    }

    /**
     * Show the post-login challenge.
     */
    public function challenge(Request $request)
    {
        $userId = session('2fa_pending_user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        return view('two-factor.challenge');
    }

    /**
     * Verify the challenge code (or recovery code).
     */
    public function verify(Request $request)
    {
        $userId = session('2fa_pending_user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = \App\Models\User::find($userId);
        if (!$user || !$user->hasTwoFactorEnabled()) {
            session()->forget('2fa_pending_user_id');
            return redirect()->route('login');
        }

        // Rate-limit
        $key = '2fa-attempt:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 6)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'code' => "Too many attempts. Try again in {$seconds} seconds.",
            ]);
        }

        $code = trim((string) $request->input('code'));

        $isValid = false;
        $usedRecovery = false;

        // Try TOTP first
        if (preg_match('/^\d{6}$/', str_replace(' ', '', $code))) {
            $isValid = Totp::verify($user->two_factor_secret, str_replace(' ', '', $code));
        } else {
            // Try recovery code
            if ($user->consumeRecoveryCode($code)) {
                $isValid = true;
                $usedRecovery = true;
            }
        }

        if (!$isValid) {
            RateLimiter::hit($key, 60);

            AuditLog::record(
                action: 'failed_login',
                subject: $user,
                description: 'Failed 2FA verification',
            );

            throw ValidationException::withMessages([
                'code' => 'Invalid code. ' . (6 - RateLimiter::attempts($key)) . ' attempts remaining.',
            ]);
        }

        RateLimiter::clear($key);

        // Complete the login that the password step started
        \Illuminate\Support\Facades\Auth::login($user, session('2fa_pending_remember', false));
        session()->forget(['2fa_pending_user_id', '2fa_pending_remember']);
        session(['2fa_passed_at' => now()->timestamp]);

        $request->session()->regenerate();

        AuditLog::record(
            action: 'login',
            subject: $user,
            description: $usedRecovery
                ? "User logged in with 2FA recovery code: {$user->email}"
                : "User logged in with 2FA: {$user->email}",
        );

        if ($usedRecovery) {
            session()->flash('warning', 'You used a recovery code. We recommend regenerating your codes.');
        }

        return redirect()->intended(route('dashboard'));
    }
}
