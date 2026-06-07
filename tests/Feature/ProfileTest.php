<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Clinovia does not expose a self-service /profile route.
 * User profile management (name, email, password) is handled by the
 * administrator via Admin → Users → Edit, or via the password-change
 * form on the login screen.
 *
 * These tests verify Clinovia-specific profile-adjacent behaviour.
 */
class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Clinovia has no self-service /profile page — authenticated users
     * hitting /profile receive a 404.
     */
    public function test_no_self_service_profile_route_exists(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(404);
    }

    /**
     * Authenticated users can change their own password via PUT /password.
     * Laravel Breeze's PasswordController is still active for this.
     */
    public function test_user_can_update_own_password(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $response = $this
            ->actingAs($user)
            ->from('/dashboard')
            ->put('/password', [
                'current_password'      => 'password',
                'password'              => 'NewStr0ng!Pass',
                'password_confirmation' => 'NewStr0ng!Pass',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/dashboard');
    }

    /**
     * Wrong current password is rejected when updating password.
     */
    public function test_correct_current_password_required_for_update(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $response = $this
            ->actingAs($user)
            ->from('/dashboard')
            ->put('/password', [
                'current_password'      => 'wrong-password',
                'password'              => 'NewStr0ng!Pass',
                'password_confirmation' => 'NewStr0ng!Pass',
            ]);

        // PasswordController uses validateWithBag('updatePassword', ...) so errors
        // land in the named 'updatePassword' bag, not the default bag.
        $response->assertSessionHasErrorsIn('updatePassword', 'current_password');
    }
}
