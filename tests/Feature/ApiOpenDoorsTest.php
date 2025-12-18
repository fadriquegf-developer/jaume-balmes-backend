<?php

namespace Tests\Feature;

use App\Models\OpenDoorSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ApiOpenDoorsTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_returns_available_sessions(): void
    {
        OpenDoorSession::factory()->create([
            'status' => 'published',
            'is_active' => true,
            'session_date' => now()->addDays(10),
        ]);

        OpenDoorSession::factory()->draft()->create();

        $response = $this->getJson('/api/open-doors/sessions');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'sessions' => [
                '*' => ['id', 'title', 'session_date']
            ]
        ]);
        $response->assertJsonCount(1, 'sessions');
    }

    public function test_api_registration_creates_record(): void
    {
        Mail::fake();

        $session = OpenDoorSession::factory()->create([
            'status' => 'published',
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/open-doors/register', [
            'open_door_session_id' => $session->id,
            'student_name' => 'API Test',
            'student_surname' => 'Student',
            'tutor_name' => 'API Test',
            'tutor_surname' => 'Tutor',
            'tutor_email' => 'api@test.com',
            'tutor_phone' => '612345678',
            'tutor_relationship' => 'father',
            'privacy_accepted' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('open_door_registrations', [
            'tutor_email' => 'api@test.com',
        ]);
    }

    public function test_api_returns_validation_errors(): void
    {
        $response = $this->postJson('/api/open-doors/register', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['open_door_session_id', 'student_name', 'tutor_email']);
    }
}
