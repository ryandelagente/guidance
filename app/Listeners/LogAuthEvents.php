<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Auth;

class LogAuthEvents
{
    public function handleLogin(Login $event): void
    {
        AuditLog::record(
            action: 'login',
            subject: $event->user,
            description: "User logged in: {$event->user->email}",
        );
    }

    public function handleLogout(Logout $event): void
    {
        if ($event->user) {
            AuditLog::record(
                action: 'logout',
                subject: $event->user,
                description: "User logged out: {$event->user->email}",
            );
        }
    }

    public function handleFailed(Failed $event): void
    {
        $email = $event->credentials['email'] ?? 'unknown';
        AuditLog::create([
            'user_id'     => null,
            'action'      => 'failed_login',
            'description' => "Failed login attempt for: {$email}",
            'ip_address'  => request()?->ip(),
            'user_agent'  => request()?->userAgent() ? substr(request()->userAgent(), 0, 500) : null,
        ]);
    }

    public function subscribe($events): array
    {
        return [
            Login::class  => 'handleLogin',
            Logout::class => 'handleLogout',
            Failed::class => 'handleFailed',
        ];
    }
}
