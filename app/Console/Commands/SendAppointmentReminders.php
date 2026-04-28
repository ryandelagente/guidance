<?php

namespace App\Console\Commands;

use App\Mail\AppointmentReminder;
use App\Models\Appointment;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class SendAppointmentReminders extends Command
{
    protected $signature = 'app:send-appointment-reminders
                            {--date= : Override target date (YYYY-MM-DD), default = tomorrow}
                            {--dry-run : Print what would be sent without actually sending}';

    protected $description = 'Email reminders for appointments scheduled tomorrow (run daily via scheduler)';

    public function handle(): int
    {
        $target = $this->option('date')
            ? Carbon::parse($this->option('date'))
            : now()->addDay();

        $isDry = $this->option('dry-run');

        $this->info("Looking for confirmed appointments on {$target->toDateString()}…");

        $appointments = Appointment::with(['studentProfile.user', 'counselor'])
            ->whereDate('appointment_date', $target->toDateString())
            ->where('status', 'confirmed')
            ->whereNull('reminder_sent_at')
            ->get();

        if ($appointments->isEmpty()) {
            $this->info('No appointments need reminders.');
            return self::SUCCESS;
        }

        $this->info("Found {$appointments->count()} appointment(s) needing reminders.");
        $studentSent = 0;
        $counselorSent = 0;

        foreach ($appointments as $appt) {
            $studentEmail   = $appt->studentProfile?->user?->email;
            $counselorEmail = $appt->counselor?->email;

            $row = sprintf(
                '  • #%d — %s with %s @ %s',
                $appt->id,
                $appt->studentProfile?->full_name ?? '—',
                $appt->counselor?->name ?? '—',
                substr($appt->start_time, 0, 5)
            );
            $this->line($row);

            if ($isDry) {
                $this->line("    [dry-run] would email: {$studentEmail}, {$counselorEmail}");
                continue;
            }

            try {
                $studentUser = $appt->studentProfile?->user;
                $counselor   = $appt->counselor;

                // Email channel (respects per-user prefs if set)
                if ($studentEmail && (!$studentUser || $studentUser->wantsNotification('appointment_reminder', 'email'))) {
                    Mail::to($studentEmail)->queue(new AppointmentReminder($appt, 'student'));
                    $studentSent++;
                }
                if ($counselorEmail && (!$counselor || $counselor->wantsNotification('appointment_reminder', 'email'))) {
                    Mail::to($counselorEmail)->queue(new AppointmentReminder($appt, 'counselor'));
                    $counselorSent++;
                }

                // SMS channel — only if opted in + has phone (free fallback to log driver)
                $smsBody = "CHMSU Guidance reminder: appointment tomorrow " . $appt->appointment_date->format('M d') . " at " . substr($appt->start_time, 0, 5) . ($appt->meeting_type === 'virtual' ? " (virtual)" : " at the Guidance Office") . ".";
                if ($studentUser) {
                    \App\Services\Sms::sendToUser($studentUser, 'appointment_reminder', $smsBody);
                }
                if ($counselor) {
                    \App\Services\Sms::sendToUser($counselor, 'appointment_reminder',
                        "Appt reminder: " . ($appt->studentProfile?->full_name ?? 'student') . " tomorrow " . $appt->appointment_date->format('M d') . " at " . substr($appt->start_time, 0, 5));
                }

                $appt->update(['reminder_sent_at' => now()]);
            } catch (\Throwable $e) {
                $this->error("    Failed: {$e->getMessage()}");
            }
        }

        if (!$isDry) {
            $this->info("✓ Sent {$studentSent} student reminder(s) and {$counselorSent} counselor reminder(s).");
        }

        return self::SUCCESS;
    }
}
