<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function users_can_login_with_valid_credentials()
    {
        // Create a user with valid credentials
        $user = User::factory()->create([
            'password' => Hash::make('12345678'), // Password yang sama dengan kredensial login
        ]);

        // Attempt to login with the valid credentials
        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => '12345678',
        ]);

        // Debugging
        $response->dump();

        // Assert that the user is redirected to the dashboard page
        $response->assertRedirect(route('register'));

        // Assert that the user is authenticated
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function users_cannot_login_with_invalid_credentials()
    {
        // Create a user with valid credentials
        $user = User::factory()->create([
            'password' => Hash::make('12345678'), // Password yang valid
        ]);

        // Debugging
        $user = User::first(); // Fetch user to verify it exists
        if ($user === null) {
            $this->fail('No user found in the database.');
        }

        // Attempt to login with invalid credentials
        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrongpassword', // Password yang salah
        ]);

        // Assert that the user is redirected back to the login page
        $response->assertRedirect(route('dashboard'));

        // Assert that the user is not authenticated
        $this->assertGuest();
    }
}
