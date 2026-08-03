<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_existing_user_can_attempt_login_without_validation_error(): void
    {
        $user = User::create([
            'username' => 'tester',
            'phonenumber' => '+50688888888',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
        $response->assertJsonPath('status', 'error');
    }

    public function test_ldap_login_routes_are_available_for_both_endpoint_names(): void
    {
        $response = $this->postJson('/api/ldap/login', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['username', 'password']);

        $legacyResponse = $this->postJson('/api/auth/ldap/login', []);

        $legacyResponse->assertStatus(422);
        $legacyResponse->assertJsonValidationErrors(['username', 'password']);
    }

    public function test_ldap_login_accepts_email_alias_for_username_field(): void
    {
        $response = $this->postJson('/api/ldap/login', [
            'email' => 'someone@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(401);
        $response->assertJsonPath('message', 'Usuario no encontrado');
    }
}
