<?php

namespace Tests\Unit;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_an_event()
    {
        $event = Event::factory()->create([
            'capacity' => 100,
        ]);

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'capacity' => 100,
        ]);
    }

    /** @test */
    public function capacity_must_be_positive()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Event::factory()->create(['capacity' => -5]);
    }
}
