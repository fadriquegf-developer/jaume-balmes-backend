<?php

namespace Tests\Feature;

use App\Mail\OpenDoorReminder;
use App\Models\OpenDoorRegistration;
use App\Models\OpenDoorSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OpenDoorReminderTest extends TestCase
{
    use RefreshDatabase;

    public function test_reminder_command_sends_emails_7_days_before(): void
    {
        Mail::fake();

        $session = OpenDoorSession::factory()->create([
            'session_date' => now()->addDays(7)->toDateString(),
            'status' => 'published',
        ]);

        $registration = OpenDoorRegistration::factory()->confirmed()->create([
            'open_door_session_id' => $session->id,
            'tutor_email' => 'reminder@test.com',
        ]);

        $this->artisan('open-doors:send-reminders')
            ->assertExitCode(0);

        Mail::assertQueued(OpenDoorReminder::class, function ($mail) {
            return $mail->hasTo('reminder@test.com');
        });
    }

    public function test_reminder_not_sent_to_pending_registrations(): void
    {
        Mail::fake();

        $session = OpenDoorSession::factory()->create([
            'session_date' => now()->addDays(7)->toDateString(),
        ]);

        OpenDoorRegistration::factory()->create([
            'open_door_session_id' => $session->id,
            'status' => 'pending',
        ]);

        $this->artisan('open-doors:send-reminders');

        Mail::assertNothingQueued();
    }

    public function test_reminder_not_sent_to_cancelled_registrations(): void
    {
        Mail::fake();

        $session = OpenDoorSession::factory()->create([
            'session_date' => now()->addDays(7)->toDateString(),
        ]);

        OpenDoorRegistration::factory()->cancelled()->create([
            'open_door_session_id' => $session->id,
        ]);

        $this->artisan('open-doors:send-reminders');

        Mail::assertNothingQueued();
    }

    public function test_reminder_not_sent_for_sessions_not_in_7_days(): void
    {
        Mail::fake();

        $session = OpenDoorSession::factory()->create([
            'session_date' => now()->addDays(5)->toDateString(),
        ]);

        OpenDoorRegistration::factory()->confirmed()->create([
            'open_door_session_id' => $session->id,
        ]);

        $this->artisan('open-doors:send-reminders');

        Mail::assertNothingQueued();
    }
}
