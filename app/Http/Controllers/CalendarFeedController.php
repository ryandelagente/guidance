<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CalendarFeedController extends Controller
{
    /**
     * Settings page — show feed URL and let user regenerate the token.
     */
    public function settings(Request $request)
    {
        $user = $request->user();
        abort_unless($user->isStaff(), 403);

        $token = $user->ensureCalendarToken();
        $feedUrl = route('calendar.feed', ['token' => $token]);

        return view('calendar-feed.settings', compact('feedUrl', 'token'));
    }

    public function regenerate(Request $request)
    {
        $user = $request->user();
        abort_unless($user->isStaff(), 403);

        $user->regenerateCalendarToken();
        return redirect()->route('calendar-feed.settings')
            ->with('success', 'Calendar token regenerated. Old subscription URLs will stop working immediately.');
    }

    /**
     * The actual .ics feed — token-authenticated, no login required.
     * Subscribers (Google Cal, Outlook, Apple Calendar) hit this URL periodically.
     */
    public function feed(string $token)
    {
        $user = User::where('calendar_feed_token', $token)->first();

        if (!$user || !$user->isStaff()) {
            abort(404);
        }

        $events = $this->buildEvents($user);
        $body = $this->renderIcs($events, $user);

        return response($body, 200, [
            'Content-Type'        => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'inline; filename="chmsu-gms-' . substr($user->calendar_feed_token, 0, 8) . '.ics"',
            'Cache-Control'       => 'private, max-age=300',
        ]);
    }

    /**
     * Build event list — appointments (next 90 days + past 30) + workshops the user organized/RSVPed to.
     */
    private function buildEvents(User $user): array
    {
        $events = [];

        // Appointments where user is the counselor
        $appts = Appointment::with('studentProfile')
            ->where('counselor_id', $user->id)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->whereBetween('appointment_date', [now()->subDays(30), now()->addDays(90)])
            ->get();

        foreach ($appts as $a) {
            $start = $a->appointment_date->setTimeFromTimeString($a->start_time);
            $end   = $a->appointment_date->setTimeFromTimeString($a->end_time);
            $events[] = [
                'uid'      => 'appt-' . $a->id . '@chmsu-gms',
                'summary'  => 'Counseling: ' . ($a->studentProfile?->full_name ?? 'Student') . ' (' . ucwords(str_replace('_', ' ', $a->appointment_type)) . ')',
                'start'    => $start,
                'end'      => $end,
                'location' => $a->meeting_type === 'virtual' ? 'Virtual' : 'CHMSU Guidance Office',
                'description' => trim(
                    'Status: ' . ucfirst($a->status) . "\n" .
                    'Mode: ' . ucwords(str_replace('_', ' ', $a->meeting_type)) . "\n" .
                    ($a->student_concern ? "Concern: " . $a->student_concern . "\n" : '') .
                    ($a->meeting_link ? "Meeting Link: " . $a->meeting_link . "\n" : '')
                ),
                'url'      => $a->meeting_link ?? null,
            ];
        }

        // Workshops organized OR RSVPed to
        $workshops = Workshop::with('rsvps')
            ->where(function ($q) use ($user) {
                $q->where('organizer_id', $user->id)
                  ->orWhereHas('rsvps', fn ($r) => $r->where('user_id', $user->id)->whereIn('status', ['registered','attended']));
            })
            ->whereBetween('starts_at', [now()->subDays(30), now()->addDays(90)])
            ->where('status', '!=', 'cancelled')
            ->get();

        foreach ($workshops as $w) {
            $events[] = [
                'uid'      => 'workshop-' . $w->id . '@chmsu-gms',
                'summary'  => '🎓 ' . $w->title,
                'start'    => $w->starts_at,
                'end'      => $w->ends_at,
                'location' => $w->venue,
                'description' => $w->description . ($w->meeting_link ? "\n\nMeeting Link: " . $w->meeting_link : ''),
                'url'      => $w->meeting_link ?? null,
            ];
        }

        return $events;
    }

    private function renderIcs(array $events, User $user): string
    {
        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//CHMSU GMS//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'X-WR-CALNAME:CHMSU Guidance — ' . $this->escape($user->name),
            'X-WR-TIMEZONE:Asia/Manila',
            'X-WR-CALDESC:Counseling appointments and workshops',
        ];

        $now = now()->setTimezone('UTC')->format('Ymd\THis\Z');

        foreach ($events as $e) {
            $startUtc = $e['start']->copy()->setTimezone('UTC')->format('Ymd\THis\Z');
            $endUtc   = $e['end']->copy()->setTimezone('UTC')->format('Ymd\THis\Z');

            $lines[] = 'BEGIN:VEVENT';
            $lines[] = 'UID:' . $e['uid'];
            $lines[] = 'DTSTAMP:' . $now;
            $lines[] = 'DTSTART:' . $startUtc;
            $lines[] = 'DTEND:' . $endUtc;
            $lines[] = 'SUMMARY:' . $this->escape($e['summary']);
            if (!empty($e['location'])) {
                $lines[] = 'LOCATION:' . $this->escape($e['location']);
            }
            if (!empty($e['description'])) {
                $lines[] = 'DESCRIPTION:' . $this->escape($e['description']);
            }
            if (!empty($e['url'])) {
                $lines[] = 'URL:' . $e['url'];
            }
            $lines[] = 'END:VEVENT';
        }

        $lines[] = 'END:VCALENDAR';
        return implode("\r\n", $lines) . "\r\n";
    }

    private function escape(string $s): string
    {
        return str_replace(
            ["\\", ",", ";", "\r\n", "\n"],
            ["\\\\", "\\,", "\\;", "\\n", "\\n"],
            $s
        );
    }
}
