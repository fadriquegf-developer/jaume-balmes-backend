<?php

namespace Tests\Feature;

use App\Mail\OpenDoorRegistrationConfirmation;
use App\Models\OpenDoorRegistration;
use App\Models\OpenDoorSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OpenDoorRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_can_be_created_via_public_form(): void
    {
        Mail::fake();

        $session = OpenDoorSession::factory()->create([
            'status' => 'published',
            'is_active' => true,
            'session_date' => now()->addDays(10),
        ]);

        $response = $this->post('/portes-obertes', [
            'open_door_session_id' => $session->id,
            'student_name' => 'Test',
            'student_surname' => 'Student',
            'tutor_name' => 'Test',
            'tutor_surname' => 'Tutor',
            'tutor_email' => 'test@example.com',
            'tutor_phone' => '612345678',
            'tutor_relationship' => 'mother',
            'privacy_accepted' => true,
        ]);

        $response->assertRedirect('/portes-obertes/success');

        $this->assertDatabaseHas('open_door_registrations', [
            'tutor_email' => 'test@example.com',
            'status' => 'pending',
        ]);
    }

    public function test_confirmation_email_queued_on_registration(): void
    {
        Mail::fake();

        $session = OpenDoorSession::factory()->create([
            'status' => 'published',
            'is_active' => true,
        ]);

        $this->post('/portes-obertes', [
            'open_door_session_id' => $session->id,
            'student_name' => 'Test',
            'student_surname' => 'Student',
            'tutor_name' => 'Test',
            'tutor_surname' => 'Tutor',
            'tutor_email' => 'test@example.com',
            'tutor_phone' => '612345678',
            'tutor_relationship' => 'mother',
            'privacy_accepted' => true,
        ]);

        Mail::assertQueued(OpenDoorRegistrationConfirmation::class, function ($mail) {
            return $mail->hasTo('test@example.com');
        });
    }

    public function test_registration_increments_session_count(): void
    {
        Mail::fake();

        $session = OpenDoorSession::factory()->create([
            'status' => 'published',
            'is_active' => true,
            'registered_count' => 0,
        ]);

        $this->post('/portes-obertes', [
            'open_door_session_id' => $session->id,
            'student_name' => 'Test',
            'student_surname' => 'Student',
            'tutor_name' => 'Test',
            'tutor_surname' => 'Tutor',
            'tutor_email' => 'test@example.com',
            'tutor_phone' => '612345678',
            'tutor_relationship' => 'mother',
            'privacy_accepted' => true,
        ]);

        $session->refresh();
        $this->assertEquals(1, $session->registered_count);
    }

    public function test_cannot_register_to_full_session(): void
    {
        Mail::fake();

        $session = OpenDoorSession::factory()->create([
            'status' => 'published',
            'is_active' => true,
            'capacity' => 10,
            'registered_count' => 10,
        ]);

        $response = $this->post('/portes-obertes', [
            'open_door_session_id' => $session->id,
            'student_name' => 'Test',
            'student_surname' => 'Student',
            'tutor_name' => 'Test',
            'tutor_surname' => 'Tutor',
            'tutor_email' => 'test@example.com',
            'tutor_phone' => '612345678',
            'tutor_relationship' => 'mother',
            'privacy_accepted' => true,
        ]);

        // Pot redirigir amb error o mostrar error de validació
        // Depèn de com estigui implementat el controller
        $this->assertTrue(
            $response->isRedirect() || $response->status() === 422,
            'Should reject registration to full session'
        );

        // No s'hauria d'haver creat la inscripció
        $this->assertDatabaseMissing('open_door_registrations', [
            'tutor_email' => 'test@example.com',
        ]);
    }

    public function test_registration_can_be_confirmed_via_token(): void
    {
        Mail::fake();

        $session = OpenDoorSession::factory()->create();
        $registration = OpenDoorRegistration::factory()->create([
            'open_door_session_id' => $session->id,
            'status' => 'pending',
        ]);

        $response = $this->get("/portes-obertes/confirmar/{$registration->confirmation_token}");

        $response->assertStatus(200);
        $response->assertViewIs('public.open-doors.confirmed');

        $registration->refresh();
        $this->assertEquals('confirmed', $registration->status);
        $this->assertNotNull($registration->confirmed_at);
    }

    public function test_registration_can_be_cancelled_via_token(): void
    {
        Mail::fake();

        $session = OpenDoorSession::factory()->create([
            'registered_count' => 5,
        ]);

        $registration = OpenDoorRegistration::factory()->confirmed()->create([
            'open_door_session_id' => $session->id,
        ]);

        $response = $this->get("/portes-obertes/cancelar/{$registration->confirmation_token}");

        $response->assertStatus(200);
        $response->assertViewIs('public.open-doors.cancelled');

        $registration->refresh();
        $this->assertEquals('cancelled', $registration->status);

        // Verificar que el comptador s'ha decrementat
        $session->refresh();
        $this->assertEquals(4, $session->registered_count);
    }

    public function test_student_full_name_accessor(): void
    {
        $registration = OpenDoorRegistration::factory()->create([
            'student_name' => 'Marc',
            'student_surname' => 'García López',
        ]);

        $this->assertEquals('Marc García López', $registration->student_full_name);
    }
}
