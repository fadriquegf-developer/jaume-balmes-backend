<?php

namespace App\Mail;

use App\Models\PostVisitSurvey;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PostVisitSurveyInvitation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public PostVisitSurvey $survey
    ) {}

    public function envelope(): Envelope
    {
        $sessionDate = $this->survey->registration->session->session_date->format('d/m/Y');

        return new Envelope(
            subject: __('post_visit.email_subject', ['date' => $sessionDate]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.post-visit.invitation',
            with: [
                'survey' => $this->survey,
                'registration' => $this->survey->registration,
                'session' => $this->survey->registration->session,
                'surveyUrl' => route('post-visit.survey', $this->survey->survey_token),
            ],
        );
    }
}
