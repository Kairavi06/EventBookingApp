<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_lists_events()
    {
        Event::factory()->count(3)->create();

        $this->getJson('/api/events')
            ->assertStatus(200)
            ->assertJsonCount(3);
    }

    /** @test */
    public function it_creates_an_event()
    {
        $data = [
            'title' => 'New Event',
            'description' => 'Description here',
            'start_time' => now()->addDay()->toDateTimeString(),
            'end_time' => now()->addDays(2)->toDateTimeString(),
            'capacity' => 50,
        ];

        $this->postJson('/api/events', $data)
            ->assertStatus(201)
            ->assertJsonFragment(['title' => 'New Event']);

        $this->assertDatabaseHas('events', ['title' => 'New Event']);
    }

    /** @test */
    public function it_validates_event_creation()
    {
        $this->postJson('/api/events', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'start_time', 'end_time', 'capacity']);
    }

    /** @test */
    public function it_updates_an_event()
    {
        $event = Event::factory()->create();

        $updateData = [
            'title' => 'Updated Event Title',
            'capacity' => 100,
        ];

        $this->putJson("/api/events/{$event->id}", $updateData)
            ->assertStatus(200)
            ->assertJsonFragment($updateData);

        $this->assertDatabaseHas('events', $updateData);
    }

    /** @test */
    public function it_deletes_an_event()
    {
        $event = Event::factory()->create();

        $this->deleteJson("/api/events/{$event->id}")
            ->assertStatus(204);

        $this->assertSoftDeleted('events', ['id' => $event->id]);
    }
}
