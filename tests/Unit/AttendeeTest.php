<?php

namespace Tests\Unit;

use App\Models\Attendee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendeeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_an_attendee()
    {
        $attendee = Attendee::factory()->create();

        $this->assertDatabaseHas('attendees', [
            'id' => $attendee->id,
            'email' => $attendee->email,
        ]);
    }
}
