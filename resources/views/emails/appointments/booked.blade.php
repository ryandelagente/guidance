<x-mail::message>
# New Appointment Request

Hello, **{{ $appointment->counselor->name ?? 'Counselor' }}**,

A student has submitted an appointment request that requires your attention.

<x-mail::panel>
**Student:** {{ $appointment->studentProfile->full_name ?? '—' }} ({{ $appointment->studentProfile->student_id_number ?? '—' }})
**Program:** {{ $appointment->studentProfile->program ?? '—' }}, {{ $appointment->studentProfile->year_level ?? '—' }}
**Date:** {{ $appointment->appointment_date->format('l, F d, Y') }}
**Time:** {{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }}
**Type:** {{ ucwords(str_replace('_', ' ', $appointment->appointment_type)) }}
**Mode:** {{ ucfirst(str_replace('_', ' ', $appointment->meeting_type)) }}
</x-mail::panel>

@if($appointment->student_concern)
**Student's Concern:**
{{ $appointment->student_concern }}
@endif

Please log in to the Guidance Management System to confirm or reschedule this appointment.

<x-mail::button :url="route('appointments.show', $appointment)">
View Appointment
</x-mail::button>

Thank you,
{{ config('app.name') }}
</x-mail::message>
