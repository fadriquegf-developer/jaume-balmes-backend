<?php

namespace Tests\Feature;

use App\Mail\PostVisitSurveyInvitation;
use App\Models\OpenDoorRegistration;
use App\Models\OpenDoorSession;
use App\Models\PostVisitSurvey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PostVisitSurveyTest extends TestCase
{
    use RefreshDatabase;

    public function test_survey_can_be_accessed_via_token(): void
    {
        $survey = PostVisitSurvey::factory()->create();

        $response = $this->get("/enquesta-visita/{$survey->survey_token}");

        $response->assertStatus(200);
        $response->assertViewIs('public.post-visit.survey');
    }

    public function test_survey_can_be_completed(): void
    {
        $survey = PostVisitSurvey::factory()->create();

        $response = $this->post("/enquesta-visita/{$survey->survey_token}", [
            'overall_rating' => 5,
            'information_rating' => 4,
            'attention_rating' => 5,
            'facilities_rating' => 4,
            'doubts_resolved' => true,
            'liked_most' => 'Tot perfecte',
            'improvements' => 'Res a millorar',
            'enrollment_interest' => 'very_high',
            'additional_comments' => 'Gràcies!',
        ]);

        $response->assertRedirect("/enquesta-visita/{$survey->survey_token}/gracies");

        $survey->refresh();
        $this->assertEquals('completed', $survey->status);
        $this->assertEquals(5, $survey->overall_rating);
        $this->assertNotNull($survey->completed_at);
    }

    public function test_completed_survey_shows_already_completed_view(): void
    {
        $survey = PostVisitSurvey::factory()->completed()->create();

        $response = $this->get("/enquesta-visita/{$survey->survey_token}");

        $response->assertStatus(200);
        $response->assertViewIs('public.post-visit.already-completed');
    }

    public function test_expired_survey_shows_expired_view(): void
    {
        $survey = PostVisitSurvey::factory()->create([
            'status' => 'pending',
            'expires_at' => now()->subDay(),
        ]);

        $response = $this->get("/enquesta-visita/{$survey->survey_token}");

        $response->assertStatus(200);
        $response->assertViewIs('public.post-visit.expired');
    }

    public function test_send_surveys_command_sends_to_attended_registrations(): void
    {
        Mail::fake();

        $session = OpenDoorSession::factory()->create([
            'session_date' => now()->subDay()->toDateString(),
            'status' => 'completed',
        ]);

        $registration = OpenDoorRegistration::factory()->attended()->create([
            'open_door_session_id' => $session->id,
            'tutor_email' => 'attended@test.com',
        ]);

        $this->artisan('surveys:send-post-visit')
            ->assertExitCode(0);

        Mail::assertQueued(PostVisitSurveyInvitation::class, function ($mail) {
            return $mail->hasTo('attended@test.com');
        });

        $this->assertDatabaseHas('post_visit_surveys', [
            'open_door_registration_id' => $registration->id,
            'status' => 'pending',
        ]);
    }

    public function test_survey_not_sent_to_no_show(): void
    {
        Mail::fake();

        $session = OpenDoorSession::factory()->create([
            'session_date' => now()->subDay()->toDateString(),
        ]);

        OpenDoorRegistration::factory()->noShow()->create([
            'open_door_session_id' => $session->id,
        ]);

        $this->artisan('surveys:send-post-visit');

        Mail::assertNothingQueued();
    }

    public function test_average_rating_calculated_correctly(): void
    {
        $survey = PostVisitSurvey::factory()->create([
            'status' => 'completed',
            'overall_rating' => 5,
            'information_rating' => 4,
            'attention_rating' => 5,
            'facilities_rating' => 4,
        ]);

        $this->assertEquals(4.5, $survey->average_rating);
    }
}
