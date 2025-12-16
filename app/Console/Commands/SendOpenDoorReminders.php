<?php

namespace App\Console\Commands;

use App\Mail\OpenDoorReminder;
use App\Models\OpenDoorRegistration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendOpenDoorReminders extends Command
{
    protected $signature = 'open-doors:send-reminders';
    protected $description = 'Envia recordatoris per les sessions de portes obertes (7 dies abans)';

    public function handle()
    {
        $targetDate = now()->addDays(7)->toDateString();

        $registrations = OpenDoorRegistration::whereHas('session', function ($query) use ($targetDate) {
            $query->where('session_date', $targetDate);
        })
            ->where('status', 'confirmed')
            ->get();

        $count = 0;
        foreach ($registrations as $registration) {
            Mail::to($registration->tutor_email)->send(new OpenDoorReminder($registration));
            $count++;
        }

        $this->info("S'han enviat {$count} recordatoris.");
    }
}
