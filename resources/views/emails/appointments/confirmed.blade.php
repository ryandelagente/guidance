<x-mail::message>
# Your Appointment is Confirmed

Hello, **{{ $appointment->studentProfile->full_name ?? 'Student' }}**,

Your counseling appointment has been **confirmed** by the Guidance Office. Please see the details below.

<x-mail::panel>
**Counselor:** {{ $appointment->counselor->name ?? '—' }}
**Date:** {{ $appointment->appointment_date->format('l, F d, Y') }}
**Time:** {{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}
**Type:** {{ ucwords(str_replace('_', ' ', $appointment->appointment_type)) }}
**Mode:** {{ ucfirst(str_replace('_', ' ', $appointment->meeting_type)) }}
@if($appointment->meeting_link)
**Meeting Link:** {{ $appointment->meeting_link }}
@endif
</x-mail::panel>

@if($appointment->notes_for_student)
**Note from your Counselor:**
{{ $appointment->notes_for_student }}
@endif

Please arrive on time or join the virtual meeting at the scheduled time. If you need to cancel or reschedule, please do so at least 24 hours in advance through the system.

<x-mail::button :url="route('appointments.show', $appointment)">
View My Appointment
</x-mail::button>

Thank you,
{{ config('app.name') }}
**CHMSU Guidance & Counseling Office**
</x-mail::message>
