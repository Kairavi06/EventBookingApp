<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventListingTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // Create some test events
        Event::factory()->create([
            'title' => 'Tech Conference',
            'country' => 'India',
            'start_time' => now()->addDays(5),
            'end_time' => now()->addDays(6),
        ]);

        Event::factory()->create([
            'title' => 'Health Summit',
            'country' => 'USA',
            'start_time' => now()->addDays(10),
            'end_time' => now()->addDays(11),
        ]);
    }

    /** @test */
    public function it_can_paginate_events()
    {
        Event::factory(15)->create();

        $response = $this->getJson('/api/events?per_page=5');

        $response->assertStatus(200)
                 ->assertJsonFragment(['current_page' => 1])
                 ->assertJsonCount(5, 'data');
    }

    /** @test */
    public function it_can_filter_events_by_title_and_country()
    {
        $response = $this->getJson('/api/events?title=Tech&country=India');

        $response->assertStatus(200);
        $this->assertEquals(1, count($response->json('data')));
        $this->assertEquals('India', $response->json('data')[0]['country']);
        $this->assertStringContainsString('Tech', $response->json('data')[0]['title']);
    }

    /** @test */
    public function it_can_filter_events_by_start_date()
    {
        $date = now()->addDays(6)->format('Y-m-d');
        $response = $this->getJson('/api/events?date=' . $date);

        $response->assertStatus(200);
        $this->assertGreaterThan(0, count($response->json('data')));
    }
}
