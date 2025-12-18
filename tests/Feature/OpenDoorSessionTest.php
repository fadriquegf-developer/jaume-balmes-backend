<?php

namespace Tests\Feature;

use App\Models\OpenDoorSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OpenDoorSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_session_can_be_created(): void
    {
        $session = OpenDoorSession::factory()->create();

        $this->assertDatabaseHas('open_door_sessions', [
            'id' => $session->id,
        ]);
    }

    public function test_session_available_spots_calculated_correctly(): void
    {
        $session = OpenDoorSession::factory()->create([
            'capacity' => 30,
            'registered_count' => 10,
        ]);

        $this->assertEquals(20, $session->available_spots);
        $this->assertFalse($session->is_full);
    }

    public function test_session_is_full_when_capacity_reached(): void
    {
        $session = OpenDoorSession::factory()->create([
            'capacity' => 30,
            'registered_count' => 30,
        ]);

        $this->assertEquals(0, $session->available_spots);
        $this->assertTrue($session->is_full);
    }

    public function test_published_scope_returns_only_published_sessions(): void
    {
        OpenDoorSession::factory()->create(['status' => 'published']);
        OpenDoorSession::factory()->create(['status' => 'draft']);
        OpenDoorSession::factory()->create(['status' => 'cancelled']);

        $published = OpenDoorSession::published()->get();

        $this->assertCount(1, $published);
    }

    public function test_upcoming_scope_returns_future_sessions(): void
    {
        OpenDoorSession::factory()->create(['session_date' => now()->addDays(5)]);
        OpenDoorSession::factory()->create(['session_date' => now()->subDays(5)]);

        $upcoming = OpenDoorSession::upcoming()->get();

        $this->assertCount(1, $upcoming);
    }

    public function test_available_scope_combines_filters(): void
    {
        // Disponible: publicada, futura, amb places
        OpenDoorSession::factory()->create([
            'session_date' => now()->addDays(5),
            'status' => 'published',
            'is_active' => true,
            'capacity' => 30,
            'registered_count' => 10,
        ]);

        // No disponible: esborrany
        OpenDoorSession::factory()->draft()->create([
            'session_date' => now()->addDays(5),
        ]);

        // No disponible: passada
        OpenDoorSession::factory()->completed()->create();

        // No disponible: plena
        OpenDoorSession::factory()->full()->create([
            'session_date' => now()->addDays(5),
            'status' => 'published',
        ]);

        $available = OpenDoorSession::available()->get();

        $this->assertCount(1, $available);
    }
}
