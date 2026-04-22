<x-mail::message>
# Student Referral — Action Required

Hello,

A new student referral has been submitted and requires your attention.

<x-mail::panel>
**Student:** {{ $referral->studentProfile->full_name ?? '—' }} ({{ $referral->studentProfile->student_id_number ?? '—' }})
**Program:** {{ $referral->studentProfile->program ?? '—' }}, {{ $referral->studentProfile->year_level ?? '—' }}
**Referred By:** {{ $referral->referredBy->name ?? '—' }}
**Category:** {{ ucwords(str_replace('_', ' ', $referral->reason_category)) }}
**Urgency:** {{ ucfirst($referral->urgency) }}
**Date Submitted:** {{ $referral->created_at->format('F d, Y') }}
</x-mail::panel>

**Faculty's Description:**
{{ $referral->description }}

@if($referral->urgency === 'critical' || $referral->urgency === 'high')
> ⚠️ **This referral is marked as {{ strtoupper($referral->urgency) }} urgency. Please respond as soon as possible.**
@endif

Please log in to the Guidance Management System to review and take action on this referral.

<x-mail::button :url="route('referrals.show', $referral)" color="red">
Review Referral
</x-mail::button>

Thank you,
{{ config('app.name') }}
**CHMSU Guidance & Counseling Office**
</x-mail::message>
