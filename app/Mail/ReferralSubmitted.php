<?php

namespace App\Mail;

use App\Models\Referral;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReferralSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Referral $referral) {}

    public function envelope(): Envelope
    {
        $urgency = strtoupper($this->referral->urgency);
        $student = $this->referral->studentProfile->full_name ?? 'a student';
        return new Envelope(subject: "[{$urgency}] New Referral: {$student} — CHMSU Guidance");
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.referrals.submitted');
    }

    public function attachments(): array { return []; }
}
