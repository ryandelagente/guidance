<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * SSO via Google Workspace + Microsoft 365 OAuth2.
 * Pure-PHP implementation — no laravel/socialite dependency required.
 * Configure via .env:
 *   GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET
 *   MS_CLIENT_ID, MS_CLIENT_SECRET, MS_TENANT (default: common)
 *
 * Domain restriction: only @chmsu.edu.ph addresses are accepted (configurable).
 */
class SsoController extends Controller
{
    private const ALLOWED_DOMAIN = 'chmsu.edu.ph';

    private function providers(): array
    {
        return [
            'google' => [
                'enabled'     => !empty(env('GOOGLE_CLIENT_ID')),
                'client_id'   => env('GOOGLE_CLIENT_ID'),
                'client_secret' => env('GOOGLE_CLIENT_SECRET'),
                'auth_url'    => 'https://accounts.google.com/o/oauth2/v2/auth',
                'token_url'   => 'https://oauth2.googleapis.com/token',
                'userinfo_url' => 'https://www.googleapis.com/oauth2/v3/userinfo',
                'scope'       => 'openid email profile',
            ],
            'microsoft' => [
                'enabled'     => !empty(env('MS_CLIENT_ID')),
                'client_id'   => env('MS_CLIENT_ID'),
                'client_secret' => env('MS_CLIENT_SECRET'),
                'auth_url'    => 'https://login.microsoftonline.com/' . env('MS_TENANT', 'common') . '/oauth2/v2.0/authorize',
                'token_url'   => 'https://login.microsoftonline.com/' . env('MS_TENANT', 'common') . '/oauth2/v2.0/token',
                'userinfo_url' => 'https://graph.microsoft.com/v1.0/me',
                'scope'       => 'openid email profile User.Read',
            ],
        ];
    }

    public function redirect(string $provider)
    {
        $providers = $this->providers();
        if (!isset($providers[$provider]) || !$providers[$provider]['enabled']) {
            return redirect()->route('login')->with('error', "SSO with {$provider} is not configured on this server.");
        }

        $config = $providers[$provider];
        $state = Str::random(40);
        session(['sso_state' => $state, 'sso_provider' => $provider]);

        $params = http_build_query([
            'client_id'     => $config['client_id'],
            'redirect_uri'  => route('sso.callback', $provider),
            'response_type' => 'code',
            'scope'         => $config['scope'],
            'state'         => $state,
            'access_type'   => $provider === 'google' ? 'online' : null,
            'prompt'        => 'select_account',
        ]);

        return redirect($config['auth_url'] . '?' . $params);
    }

    public function callback(Request $request, string $provider)
    {
        $providers = $this->providers();
        if (!isset($providers[$provider]) || !$providers[$provider]['enabled']) {
            return redirect()->route('login')->with('error', 'SSO not configured.');
        }
        $config = $providers[$provider];

        // CSRF check
        if (!$request->state || $request->state !== session('sso_state')) {
            return redirect()->route('login')->with('error', 'Invalid SSO state. Please try again.');
        }
        session()->forget(['sso_state', 'sso_provider']);

        if (!$request->code) {
            return redirect()->route('login')->with('error', 'SSO sign-in was cancelled.');
        }

        // Exchange code for token
        try {
            $tokenResp = Http::asForm()->post($config['token_url'], [
                'client_id'     => $config['client_id'],
                'client_secret' => $config['client_secret'],
                'code'          => $request->code,
                'redirect_uri'  => route('sso.callback', $provider),
                'grant_type'    => 'authorization_code',
            ]);

            if (!$tokenResp->successful()) {
                throw new \Exception('Token exchange failed: ' . $tokenResp->body());
            }

            $accessToken = $tokenResp->json('access_token');

            $userResp = Http::withToken($accessToken)->get($config['userinfo_url']);
            if (!$userResp->successful()) {
                throw new \Exception('Failed to fetch user info');
            }

            $info = $userResp->json();
            $email = $info['email'] ?? $info['mail'] ?? $info['userPrincipalName'] ?? null;
            $name  = $info['name'] ?? $info['displayName'] ?? null;

            if (!$email) {
                throw new \Exception('No email from SSO provider');
            }

            // Domain restriction
            if (!str_ends_with(strtolower($email), '@' . self::ALLOWED_DOMAIN)) {
                AuditLog::create([
                    'user_id'     => null,
                    'action'      => 'failed_login',
                    'description' => "SSO rejected — non-CHMSU domain: {$email}",
                    'ip_address'  => $request->ip(),
                ]);
                return redirect()->route('login')->with('error', 'Only @' . self::ALLOWED_DOMAIN . ' email addresses are allowed.');
            }

            // Find existing user or auto-provision
            $user = User::where('email', $email)->first();

            if (!$user) {
                return redirect()->route('login')->with('error', "No CHMSU account found for {$email}. Contact the Guidance Office to request access.");
            }

            if (!$user->is_active) {
                return redirect()->route('login')->with('error', 'Your account is deactivated. Contact the Guidance Office.');
            }

            // Honor 2FA if user has it
            if ($user->hasTwoFactorEnabled()) {
                session([
                    '2fa_pending_user_id' => $user->id,
                    '2fa_pending_remember' => true,
                ]);
                return redirect()->route('two-factor.challenge');
            }

            Auth::login($user, true);
            $request->session()->regenerate();

            AuditLog::record(
                action: 'login',
                subject: $user,
                description: "SSO login via {$provider}: {$email}",
            );

            return redirect()->intended(route('dashboard'));

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("SSO {$provider} callback error: " . $e->getMessage());
            return redirect()->route('login')->with('error', 'SSO sign-in failed. Please try again or sign in with email and password.');
        }
    }
}
