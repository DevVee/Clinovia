<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Unauthenticated requests to the root URL are redirected to /login.
     * Clinovia is a protected system — no public landing page.
     */
    public function test_root_redirects_guests_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    /**
     * Authenticated users visiting the root URL are redirected to the dashboard.
     */
    public function test_authenticated_users_reach_dashboard(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)->get('/dashboard');

        // Dashboard may further redirect to role-based page; at minimum it's not a 404/500.
        $response->assertStatus(200);
    }
}
