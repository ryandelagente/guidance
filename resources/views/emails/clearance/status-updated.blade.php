<x-mail::message>
# Clearance Request Update

Hello, **{{ $clearance->studentProfile->full_name ?? 'Student' }}**,

Your **{{ ucfirst($clearance->clearance_type) }} Clearance** request has been updated.

<x-mail::panel>
**Clearance Type:** {{ ucfirst($clearance->clearance_type) }}
**Academic Year:** {{ $clearance->academic_year }} — {{ $clearance->semester }} Semester
**New Status:** {{ ucwords(str_replace('_', ' ', $clearance->status)) }}
**Updated On:** {{ now()->format('F d, Y') }}
</x-mail::panel>

@if($clearance->status === 'approved')
🎉 **Congratulations!** Your clearance has been approved. You may now proceed with your next steps.
@elseif($clearance->status === 'for_exit_survey')
📋 **Action Required:** Please complete the exit interview survey before your clearance can be processed.
@elseif($clearance->status === 'rejected')
❌ Your clearance request was not approved at this time. Please visit the Guidance Office for assistance.
@elseif($clearance->status === 'on_hold')
⏸️ Your clearance request is currently on hold. Please contact the Guidance Office for details.
@else
Your request is currently being processed. We will notify you of any further updates.
@endif

@if($clearance->notes)
**Note from the Guidance Office:**
{{ $clearance->notes }}
@endif

<x-mail::button :url="route('clearance.show', $clearance)">
View Clearance Status
</x-mail::button>

Thank you,
{{ config('app.name') }}
**CHMSU Guidance & Counseling Office**
</x-mail::message>
