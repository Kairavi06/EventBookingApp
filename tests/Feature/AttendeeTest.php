<?php

namespace Tests\Feature;

use App\Models\Attendee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendeeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_register_an_attendee()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ];

        $response = $this->postJson('/api/attendees', $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'John Doe',
                'email' => 'john@example.com',
            ]);

        $this->assertDatabaseHas('attendees', $data);
    }

    /** @test */
    public function cannot_register_attendee_with_duplicate_email()
    {
        Attendee::factory()->create(['email' => 'john@example.com']);

        $data = [
            'name' => 'Jane Doe',
            'email' => 'john@example.com',  // Duplicate email
        ];

        $response = $this->postJson('/api/attendees', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    /** @test */
    public function can_list_all_attendees()
    {
        Attendee::factory()->count(3)->create();

        $response = $this->getJson('/api/attendees');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /** @test */
    public function can_show_single_attendee()
    {
        $attendee = Attendee::factory()->create();

        $response = $this->getJson("/api/attendees/{$attendee->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $attendee->id,
                'name' => $attendee->name,
                'email' => $attendee->email,
            ]);
    }

    /** @test */
    public function can_update_attendee()
    {
        $attendee = Attendee::factory()->create();

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ];

        $response = $this->putJson("/api/attendees/{$attendee->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment($updateData);

        $this->assertDatabaseHas('attendees', $updateData);
    }

    /** @test */
    public function can_delete_attendee()
    {
        $attendee = Attendee::factory()->create();

        $response = $this->deleteJson("/api/attendees/{$attendee->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('attendees', ['id' => $attendee->id]);
    }
}

