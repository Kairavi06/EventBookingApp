<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Event;
use App\Models\Attendee;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */   
    public function test_prevent_overbooking()
    {
        $event = Event::factory()->create(['capacity' => 1]);
        $attendee1 = Attendee::factory()->create();
        $attendee2 = Attendee::factory()->create();

        Booking::create(['event_id' => $event->id, 'attendee_id' => $attendee1->id]);

        $response = $this->postJson('/api/bookings', [
            'event_id' => $event->id,
            'attendee_id' => $attendee2->id,
        ]);

        $response->assertStatus(400)
                 ->assertJson(['message' => 'Event is fully booked.']);
    }
    
    /** @test */
    public function test_prevent_duplicate_booking()
    {
        $event = Event::factory()->create(['capacity' => 10]);
        $attendee = Attendee::factory()->create();

        Booking::create(['event_id' => $event->id, 'attendee_id' => $attendee->id]);

        $response = $this->postJson('/api/bookings', [
            'event_id' => $event->id,
            'attendee_id' => $attendee->id,
        ]);

        $response->assertStatus(400)
                 ->assertJson(['message' => 'Duplicate booking not allowed.']);
    }

    /** @test */
    public function test_successful_booking()
    {
        $event = Event::factory()->create(['capacity' => 10]);
        $attendee = Attendee::factory()->create();

        $response = $this->postJson('/api/bookings', [
            'event_id' => $event->id,
            'attendee_id' => $attendee->id,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('bookings', [
            'event_id' => $event->id,
            'attendee_id' => $attendee->id,
        ]);
    }

    /** @test */
    public function it_validates_booking_request()
    {
        $this->postJson('/api/bookings', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['event_id', 'attendee_id']);
    }
}
