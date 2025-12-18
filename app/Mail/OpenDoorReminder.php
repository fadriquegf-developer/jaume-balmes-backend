<?php

namespace App\Mail;

use App\Models\OpenDoorRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OpenDoorReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public OpenDoorRegistration $registration
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('open_doors.reminder_subject', [
                'date' => $this->registration->session->session_date->format('d/m/Y')
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.open-doors.reminder',
            with: [
                'registration' => $this->registration,
                'session' => $this->registration->session,
                'cancelUrl' => route('open-doors.cancel', $this->registration->confirmation_token),
            ],
        );
    }
}
