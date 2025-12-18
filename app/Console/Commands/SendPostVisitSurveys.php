<?php

namespace App\Console\Commands;

use App\Mail\PostVisitSurveyInvitation;
use App\Models\OpenDoorRegistration;
use App\Models\PostVisitSurvey;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendPostVisitSurveys extends Command
{
    protected $signature = 'surveys:send-post-visit';
    protected $description = 'Envia enquestes post-visita als assistents del dia anterior';

    public function handle()
    {
        // Buscar inscripcions amb assistència ahir que no tenen enquesta
        $yesterday = now()->subDay()->toDateString();

        $registrations = OpenDoorRegistration::where('status', 'attended')
            ->whereHas('session', function ($query) use ($yesterday) {
                $query->whereDate('session_date', $yesterday);
            })
            ->whereDoesntHave('postVisitSurvey')
            ->with('session')
            ->get();

        $count = 0;
        foreach ($registrations as $registration) {
            $survey = PostVisitSurvey::create([
                'open_door_registration_id' => $registration->id,
            ]);

            Mail::to($registration->tutor_email)->send(new PostVisitSurveyInvitation($survey));
            $survey->markAsSent();

            $count++;
            $this->info("Enquesta enviada a: {$registration->tutor_email}");
        }

        $this->info("Total: {$count} enquestes enviades.");

        return Command::SUCCESS;
    }
}
