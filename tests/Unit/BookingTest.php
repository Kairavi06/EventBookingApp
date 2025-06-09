<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Attendee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_booking()
    {
        $event = Event::factory()->create(['capacity' => 10]);
        $attendee = Attendee::factory()->create();

        $booking = Booking::create([
            'event_id' => $event->id,
            'attendee_id' => $attendee->id,
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'event_id' => $event->id,
            'attendee_id' => $attendee->id,
        ]);
    }
}
