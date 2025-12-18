<?php

namespace Tests\Feature;

use App\Mail\OpenDoorRegistrationConfirmation;
use App\Mail\OpenDoorReminder;
use App\Mail\PostVisitSurveyInvitation;
use App\Models\OpenDoorRegistration;
use App\Models\OpenDoorSession;
use App\Models\PostVisitSurvey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class OpenDoorsCompleteFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_open_doors_flow(): void
    {
        Mail::fake();

        // ========================================
        // FASE 1: Admin crea sessió
        // ========================================
        $admin = User::factory()->create();

        $session = OpenDoorSession::factory()->create([
            'title' => 'Portes Obertes Test',
            'session_date' => now()->addDays(10),
            'capacity' => 30,
            'status' => 'published',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('open_door_sessions', [
            'title' => 'Portes Obertes Test',
            'status' => 'published',
        ]);

        // ========================================
        // FASE 2: Família s'inscriu via formulari públic
        // ========================================
        $registrationData = [
            'open_door_session_id' => $session->id,
            'student_name' => 'Marc',
            'student_surname' => 'García López',
            'student_birthdate' => '2010-05-15',
            'current_school' => 'Escola Pompeu Fabra',
            'current_grade' => '6è Primària',
            'tutor_name' => 'Anna',
            'tutor_surname' => 'López',
            'tutor_email' => 'anna.lopez@test.com',
            'tutor_phone' => '612345678',
            'tutor_relationship' => 'mother',
            'interested_grades' => ['eso', 'batxillerat'],
            'how_did_you_know' => 'web',
            'comments' => 'Estem molt interessats',
            'privacy_accepted' => true,
        ];

        $response = $this->post('/portes-obertes', $registrationData);
        $response->assertRedirect('/portes-obertes/success');

        $this->assertDatabaseHas('open_door_registrations', [
            'student_name' => 'Marc',
            'tutor_email' => 'anna.lopez@test.com',
            'status' => 'pending',
        ]);

        $registration = OpenDoorRegistration::where('tutor_email', 'anna.lopez@test.com')->first();
        $this->assertNotNull($registration);

        $session->refresh();
        $this->assertEquals(1, $session->registered_count);

        // ========================================
        // FASE 3: Verificar email de confirmació QUEUED
        // ========================================
        Mail::assertQueued(OpenDoorRegistrationConfirmation::class, function ($mail) {
            return $mail->hasTo('anna.lopez@test.com');
        });

        // ========================================
        // FASE 4: Família confirma assistència
        // ========================================
        $response = $this->get("/portes-obertes/confirmar/{$registration->confirmation_token}");
        $response->assertStatus(200);
        $response->assertViewIs('public.open-doors.confirmed');

        $registration->refresh();
        $this->assertEquals('confirmed', $registration->status);
        $this->assertNotNull($registration->confirmed_at);

        // ========================================
        // FASE 5: Test comanda recordatoris (7 dies abans)
        // ========================================
        $sessionIn7Days = OpenDoorSession::factory()->create([
            'session_date' => now()->addDays(7)->toDateString(),
            'status' => 'published',
        ]);

        // Crear registre per al recordatori SENSE events
        OpenDoorRegistration::withoutEvents(function () use ($sessionIn7Days) {
            return OpenDoorRegistration::create([
                'open_door_session_id' => $sessionIn7Days->id,
                'student_name' => 'Reminder',
                'student_surname' => 'Test',
                'tutor_name' => 'Reminder',
                'tutor_surname' => 'Tutor',
                'tutor_email' => 'reminder@test.com',
                'tutor_phone' => '612345678',
                'tutor_relationship' => 'father',
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

        // ========================================
        // FASE 6: Admin marca assistència
        // ========================================
        $registration->update([
            'status' => 'attended',
            'attended_at' => now(),
        ]);

        $this->assertDatabaseHas('open_door_registrations', [
            'id' => $registration->id,
            'status' => 'attended',
        ]);

        // ========================================
        // FASE 7: Crear i enviar enquesta post-visita
        // ========================================
        $survey = PostVisitSurvey::create([
            'open_door_registration_id' => $registration->id,
        ]);

        Mail::to($registration->tutor_email)->queue(new PostVisitSurveyInvitation($survey));
        $survey->markAsSent();

        Mail::assertQueued(PostVisitSurveyInvitation::class, function ($mail) {
            return $mail->hasTo('anna.lopez@test.com');
        });

        // ========================================
        // FASE 8: Família completa l'enquesta
        // ========================================
        $response = $this->get("/enquesta-visita/{$survey->survey_token}");
        $response->assertStatus(200);
        $response->assertViewIs('public.post-visit.survey');

        $surveyData = [
            'overall_rating' => 5,
            'information_rating' => 4,
            'attention_rating' => 5,
            'facilities_rating' => 4,
            'doubts_resolved' => true,
            'liked_most' => 'L\'atenció va ser excel·lent',
            'improvements' => 'Més temps per la visita',
            'enrollment_interest' => 'very_high',
            'additional_comments' => 'Molt contents!',
        ];

        $response = $this->post("/enquesta-visita/{$survey->survey_token}", $surveyData);
        $response->assertRedirect("/enquesta-visita/{$survey->survey_token}/gracies");

        $survey->refresh();
        $this->assertEquals('completed', $survey->status);

        // ========================================
        // VERIFICACIÓ FINAL
        // ========================================
        $registration->refresh();
        $this->assertEquals('attended', $registration->status);
        $this->assertNotNull($registration->postVisitSurvey);
        $this->assertEquals('completed', $registration->postVisitSurvey->status);
    }
}
