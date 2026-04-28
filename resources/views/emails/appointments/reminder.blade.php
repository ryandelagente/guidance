<x-mail::message>
# 🔔 Reminder: Your counseling appointment is tomorrow

@if($audience === 'student')
Hi **{{ $appointment->studentProfile?->first_name }}**,

This is a friendly reminder that you have a counseling appointment tomorrow. Please be on time (or message your counselor if you need to reschedule).
@else
Hi **{{ $appointment->counselor?->name }}**,

You have an appointment with **{{ $appointment->studentProfile?->full_name }}** tomorrow.
@endif

<x-mail::panel>
**Date:** {{ $appointment->appointment_date->format('l, F d, Y') }}
**Time:** {{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}
**Type:** {{ ucwords(str_replace('_', ' ', $appointment->appointment_type)) }} counseling
**Mode:** {{ $appointment->meeting_type === 'virtual' ? '🌐 Virtual' : '🏢 In-person, CHMSU Guidance Office' }}
@if($audience === 'student')
**Counselor:** {{ $appointment->counselor?->name ?? '—' }}
@else
**Student:** {{ $appointment->studentProfile?->full_name }}
@if($appointment->student_concern)
**Concern noted:** {{ $appointment->student_concern }}
@endif
@endif
</x-mail::panel>

@if($appointment->meeting_link)
**Meeting link:** [Click here to join]({{ $appointment->meeting_link }})

(Save this link — you'll need it tomorrow.)
@endif

@if($appointment->notes_for_student && $audience === 'student')
**Notes from your counselor:**
{{ $appointment->notes_for_student }}
@endif

<x-mail::button :url="route('appointments.show', $appointment)">
View Appointment Details
</x-mail::button>

@if($audience === 'student')
**Need to reschedule?** Please log in to GMS and update the appointment, or message your counselor as early as possible. Last-minute cancellations make it harder to help other students who need a slot.

If something is bothering you and you're not sure if you should still come, **please come anyway** — we're here to help.
@endif

Take care,
**CHMSU Guidance Office**
</x-mail::message>
