<?php

namespace Tests\Feature;

use App\Models\Attendee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AttendeeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_register_an_attendee_with_password()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        $response = $this->postJson('/api/attendees', $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'John Doe',
                'email' => 'john@example.com',
            ]);

        $this->assertDatabaseHas('attendees', [
            'email' => 'john@example.com',
        ]);

        $attendee = Attendee::where('email', 'john@example.com')->first();
        $this->assertTrue(Hash::check('secret123', $attendee->password));
    }

    /** @test */
    public function cannot_register_attendee_with_duplicate_email()
    {
        Attendee::factory()->create(['email' => 'john@example.com']);

        $data = [
            'name' => 'Jane Doe',
            'email' => 'john@example.com',  // Duplicate email
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        $response = $this->postJson('/api/attendees', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    /** @test */
    public function cannot_register_attendee_with_password_mismatch()
    {
        $data = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'different',
        ];

        $response = $this->postJson('/api/attendees', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }

    /** @test */
    public function can_show_authenticated_attendee_profile()
    {
        $attendee = Attendee::factory()->create();

        $response = $this->actingAs($attendee)->getJson('/api/attendees');

        $response->assertStatus(200)
            ->assertJson([
                'id' => $attendee->id,
                'name' => $attendee->name,
                'email' => $attendee->email,
            ]);
    }

    /** @test */
    public function can_update_authenticated_attendee_profile_with_password()
    {
        $attendee = Attendee::factory()->create();

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'password' => 'newsecret123',
            'password_confirmation' => 'newsecret123',
        ];

        $response = $this->actingAs($attendee)->putJson('/api/attendees', $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
            ]);

        $this->assertDatabaseHas('attendees', [
            'id' => $attendee->id,
            'email' => 'updated@example.com',
        ]);

        $updatedAttendee = Attendee::find($attendee->id);
        $this->assertTrue(Hash::check('newsecret123', $updatedAttendee->password));
    }

    /** @test */
    public function can_delete_authenticated_attendee_profile()
    {
        $attendee = Attendee::factory()->create();

        $response = $this->actingAs($attendee)->deleteJson('/api/attendees');

        $response->assertStatus(204);

        $this->assertSoftDeleted('attendees', ['id' => $attendee->id]);
    }
}
