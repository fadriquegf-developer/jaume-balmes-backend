<?php

namespace App\Console\Commands;

use App\Mail\OpenDoorReminder;
use App\Models\OpenDoorRegistration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendOpenDoorReminders extends Command
{
    protected $signature = 'open-doors:send-reminders';
    protected $description = 'Envia recordatoris als inscrits amb sessions en 7 dies';

    public function handle(): int
    {
        $targetDate = now()->addDays(7)->toDateString();

        $registrations = OpenDoorRegistration::where('status', 'confirmed')
            ->whereHas('session', function ($query) use ($targetDate) {
                $query->whereDate('session_date', $targetDate);
            })
            ->with('session')
            ->get();

        $count = 0;
        foreach ($registrations as $registration) {
            Mail::to($registration->tutor_email)
                ->queue(new OpenDoorReminder($registration));
            $count++;
            $this->info("Recordatori enviat a: {$registration->tutor_email}");
        }

        $this->info("Total: {$count} recordatoris enviats.");

        return Command::SUCCESS;
    }
}
