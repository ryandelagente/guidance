<?php

namespace App\Mail;

use App\Models\ClearanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClearanceStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ClearanceRequest $clearance) {}

    public function envelope(): Envelope
    {
        $status = ucwords(str_replace('_', ' ', $this->clearance->status));
        return new Envelope(subject: "Clearance Request Update: {$status} — CHMSU Guidance Office");
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.clearance.status-updated');
    }

    public function attachments(): array { return []; }
}
