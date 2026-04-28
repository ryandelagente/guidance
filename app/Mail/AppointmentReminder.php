<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Appointment $appointment, public string $audience = 'student')
    {
    }

    public function envelope(): Envelope
    {
        $student = $this->appointment->studentProfile?->full_name ?? 'Student';
        return new Envelope(
            subject: "Reminder: Your appointment tomorrow — CHMSU Guidance ({$student})",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.appointments.reminder',
            with: [
                'appointment' => $this->appointment,
                'audience'    => $this->audience,
            ],
        );
    }
}
