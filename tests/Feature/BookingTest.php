<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Event;
use App\Models\Attendee;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_prevent_overbooking()
    {
        $event = Event::factory()->create(['capacity' => 1]);
        $attendee1 = Attendee::factory()->create();
        $attendee2 = Attendee::factory()->create();

        // Create initial booking for attendee1
        Booking::create([
            'event_id' => $event->id,
            'attendee_id' => $attendee1->id,
            'booking_date' => Carbon::now()->toDateString(),
        ]);

        // Attendee2 tries to book same event and date
        $response = $this->actingAs($attendee2, 'sanctum')->postJson('/api/bookings', [
            'event_id' => $event->id,
            'booking_date' => Carbon::now()->toDateString(),
        ]);

        $response->assertStatus(400)
                 ->assertJson(['message' => 'Event is fully booked.']);
    }

    /** @test */
    public function test_prevent_duplicate_booking()
    {
        $event = Event::factory()->create(['capacity' => 10]);
        $attendee = Attendee::factory()->create();

        // Create booking for this attendee
        Booking::create([
            'event_id' => $event->id,
            'attendee_id' => $attendee->id,
            'booking_date' => Carbon::now()->toDateString(),
        ]);

        // Try booking again for the same event and date by same attendee
        $response = $this->actingAs($attendee, 'sanctum')->postJson('/api/bookings', [
            'event_id' => $event->id,
            'booking_date' => Carbon::now()->toDateString(),
        ]);

        $response->assertStatus(400)
                 ->assertJson(['message' => 'Duplicate booking not allowed.']);
    }

    /** @test */
    public function test_successful_booking()
    {
        $event = Event::factory()->create(['capacity' => 10]);
        $attendee = Attendee::factory()->create();

        $bookingDate = Carbon::now()->toDateString();

        $response = $this->actingAs($attendee, 'sanctum')->postJson('/api/bookings', [
            'event_id' => $event->id,
            'booking_date' => $bookingDate,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('bookings', [
            'event_id' => $event->id,
            'attendee_id' => $attendee->id,
            'booking_date' => $bookingDate,
        ]);
    }

    /** @test */
    public function it_validates_booking_request()
    {
        $attendee = Attendee::factory()->create();

        $this->actingAs($attendee, 'sanctum')
            ->postJson('/api/bookings', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['event_id', 'booking_date']);
    }

    /** @test */
    public function it_validates_booking_date_is_valid_and_within_event_dates()
    {
        $event = Event::factory()->create([
            'start_date' => '2025-06-10',
            'end_date' => '2025-06-15',
            'capacity' => 10,
        ]);
        
        $attendee = Attendee::factory()->create();

        // Booking date before event start date
        $response = $this->actingAs($attendee, 'sanctum')->postJson('/api/bookings', [
            'event_id' => $event->id,
            'booking_date' => '2025-06-05',
        ]);
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['booking_date']);

        // Booking date after event end date
        $response = $this->actingAs($attendee, 'sanctum')->postJson('/api/bookings', [
            'event_id' => $event->id,
            'booking_date' => '2025-06-20',
        ]);
        
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['booking_date']);

        // Valid booking date inside event date range
        $response = $this->actingAs($attendee, 'sanctum')->postJson('/api/bookings', [
            'event_id' => $event->id,
            'booking_date' => '2025-06-12',
        ]);
        $response->assertStatus(201);
    }
}
