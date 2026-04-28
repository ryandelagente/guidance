<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SMS abstraction. Supports:
 *   - Semaphore (https://semaphore.co — primary PH SMS gateway)
 *   - Twilio (international fallback)
 *   - "log" driver for development (writes to storage/logs/laravel.log instead of sending)
 *
 * Configure via .env:
 *   SMS_DRIVER=semaphore|twilio|log
 *   SEMAPHORE_API_KEY=…
 *   SEMAPHORE_SENDER_NAME=CHMSU
 *
 * Use:
 *   Sms::send('+639171234567', 'Hello!');
 *   Sms::sendToUser($user, 'Reminder: appointment tomorrow.');
 */
class Sms
{
    public static function send(string $to, string $message): bool
    {
        $driver = config('services.sms.driver', env('SMS_DRIVER', 'log'));

        return match ($driver) {
            'semaphore' => self::sendSemaphore($to, $message),
            'twilio'    => self::sendTwilio($to, $message),
            default     => self::sendLog($to, $message),
        };
    }

    /**
     * Send to a user respecting their notification preferences.
     */
    public static function sendToUser(\App\Models\User $user, string $event, string $message): bool
    {
        if (!$user->phone_number) return false;
        if (!$user->wantsNotification($event, 'sms')) return false;

        return self::send($user->phone_number, $message);
    }

    private static function sendSemaphore(string $to, string $message): bool
    {
        $key    = env('SEMAPHORE_API_KEY');
        $sender = env('SEMAPHORE_SENDER_NAME', 'CHMSU');

        if (!$key) {
            Log::warning('SMS: Semaphore driver selected but SEMAPHORE_API_KEY is not set. Falling back to log.');
            return self::sendLog($to, $message);
        }

        try {
            $response = Http::asForm()->post('https://api.semaphore.co/api/v4/messages', [
                'apikey'     => $key,
                'number'     => $to,
                'message'    => $message,
                'sendername' => $sender,
            ]);

            if ($response->successful()) {
                Log::info("SMS sent via Semaphore to {$to}");
                return true;
            }
            Log::error('SMS Semaphore failed: ' . $response->body());
        } catch (\Throwable $e) {
            Log::error('SMS Semaphore exception: ' . $e->getMessage());
        }
        return false;
    }

    private static function sendTwilio(string $to, string $message): bool
    {
        $sid   = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        $from  = env('TWILIO_FROM');

        if (!$sid || !$token || !$from) {
            Log::warning('SMS: Twilio driver selected but credentials missing. Falling back to log.');
            return self::sendLog($to, $message);
        }

        try {
            $response = Http::withBasicAuth($sid, $token)->asForm()->post(
                "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json",
                ['From' => $from, 'To' => $to, 'Body' => $message]
            );

            if ($response->successful()) {
                Log::info("SMS sent via Twilio to {$to}");
                return true;
            }
            Log::error('SMS Twilio failed: ' . $response->body());
        } catch (\Throwable $e) {
            Log::error('SMS Twilio exception: ' . $e->getMessage());
        }
        return false;
    }

    private static function sendLog(string $to, string $message): bool
    {
        Log::channel('single')->info("[SMS-LOG] To: {$to}\n{$message}");
        return true;
    }
}
