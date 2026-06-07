<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test self-service profile route is accessible.
     */
    public function test_profile_page_is_accessible(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    /**
     * Test user can update their own profile information.
     */
    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)->put('/profile', [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);

        $response->assertRedirect('/profile');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    /**
     * Test user can upload their avatar.
     */
    public function test_avatar_can_be_uploaded(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['is_active' => true]);

        $file = UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($user)->post('/profile/avatar', [
            'avatar' => $file,
        ]);

        $response->assertRedirect('/profile');
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
    }

    /**
     * Test user can remove their avatar.
     */
    public function test_avatar_can_be_removed(): void
    {
        Storage::fake('public');
        $user = User::factory()->create([
            'is_active' => true,
            'avatar' => 'avatars/test.jpg',
        ]);
        Storage::disk('public')->put('avatars/test.jpg', 'fake image content');

        $response = $this->actingAs($user)->delete('/profile/avatar');

        $response->assertRedirect('/profile');
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertNull($user->avatar);
        Storage::disk('public')->assertMissing('avatars/test.jpg');
    }

    /**
     * Authenticated users can change their own password via PUT /profile/password.
     */
    public function test_user_can_update_own_password(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/profile/password', [
                'current_password'      => 'password',
                'password'              => 'NewStr0ng!Pass',
                'password_confirmation' => 'NewStr0ng!Pass',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/profile');
    }

    /**
     * Wrong current password is rejected when updating password.
     */
    public function test_correct_current_password_required_for_update(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/profile/password', [
                'current_password'      => 'wrong-password',
                'password'              => 'NewStr0ng!Pass',
                'password_confirmation' => 'NewStr0ng!Pass',
            ]);

        $response->assertSessionHasErrorsIn('updatePassword', 'current_password');
    }
}
