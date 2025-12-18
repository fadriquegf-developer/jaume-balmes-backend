<?php

namespace Tests\Feature;

use App\Mail\OpenDoorReminder;
use App\Models\OpenDoorRegistration;
use App\Models\OpenDoorSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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

        // Crear registre sense disparar events
        OpenDoorRegistration::withoutEvents(function () use ($session) {
            return OpenDoorRegistration::create([
                'open_door_session_id' => $session->id,
                'student_name' => 'Test',
                'student_surname' => 'Student',
                'tutor_name' => 'Test',
                'tutor_surname' => 'Tutor',
                'tutor_email' => 'reminder@test.com',
                'tutor_phone' => '612345678',
                'tutor_relationship' => 'mother',
                'status' => 'confirmed',
                'confirmed_at' => now(),
                'confirmation_token' => Str::uuid(),
            ]);
        });

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

        OpenDoorRegistration::withoutEvents(function () use ($session) {
            return OpenDoorRegistration::create([
                'open_door_session_id' => $session->id,
                'student_name' => 'Test',
                'student_surname' => 'Student',
                'tutor_name' => 'Test',
                'tutor_surname' => 'Tutor',
                'tutor_email' => 'pending@test.com',
                'tutor_phone' => '612345678',
                'tutor_relationship' => 'mother',
                'status' => 'pending',
                'confirmation_token' => Str::uuid(),
            ]);
        });

        $this->artisan('open-doors:send-reminders');

        Mail::assertNothingQueued();
    }

    public function test_reminder_not_sent_to_cancelled_registrations(): void
    {
        Mail::fake();

        $session = OpenDoorSession::factory()->create([
            'session_date' => now()->addDays(7)->toDateString(),
        ]);

        OpenDoorRegistration::withoutEvents(function () use ($session) {
            return OpenDoorRegistration::create([
                'open_door_session_id' => $session->id,
                'student_name' => 'Test',
                'student_surname' => 'Student',
                'tutor_name' => 'Test',
                'tutor_surname' => 'Tutor',
                'tutor_email' => 'cancelled@test.com',
                'tutor_phone' => '612345678',
                'tutor_relationship' => 'mother',
                'status' => 'cancelled',
                'confirmation_token' => Str::uuid(),
            ]);
        });

        $this->artisan('open-doors:send-reminders');

        Mail::assertNothingQueued();
    }

    public function test_reminder_not_sent_for_sessions_not_in_7_days(): void
    {
        Mail::fake();

        $session = OpenDoorSession::factory()->create([
            'session_date' => now()->addDays(5)->toDateString(),
        ]);

        OpenDoorRegistration::withoutEvents(function () use ($session) {
            return OpenDoorRegistration::create([
                'open_door_session_id' => $session->id,
                'student_name' => 'Test',
                'student_surname' => 'Student',
                'tutor_name' => 'Test',
                'tutor_surname' => 'Tutor',
                'tutor_email' => 'notyet@test.com',
                'tutor_phone' => '612345678',
                'tutor_relationship' => 'mother',
                'status' => 'confirmed',
                'confirmed_at' => now(),
                'confirmation_token' => Str::uuid(),
            ]);
        });

        $this->artisan('open-doors:send-reminders');

        Mail::assertNothingQueued();
    }
}
