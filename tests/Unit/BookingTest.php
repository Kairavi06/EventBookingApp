<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Attendee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_booking_with_authenticated_attendee()
    {
        $event = Event::factory()->create(['capacity' => 10]);
        $authenticatedAttendee = Attendee::factory()->create();

        $bookingDate = Carbon::now()->toDateString();

        // Simulate booking creation with attendee taken from "auth"
        $booking = Booking::create([
            'event_id' => $event->id,
            'attendee_id' => $authenticatedAttendee->id,
            'booking_date' => $bookingDate,
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'event_id' => $event->id,
            'attendee_id' => $authenticatedAttendee->id,
            'booking_date' => $bookingDate,
        ]);
    }
}
