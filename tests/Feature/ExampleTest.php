<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_ban_user(): void
    {
        $user = \App\Models\User::factory()->create(['status' => 'active']);

        $response = $this->post(route('ban_user', ['userId' => $user->id]));

        $response->assertStatus(302);
        $this->assertEquals('banned', $user->fresh()->status);
    }
}
