<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created_without_password_confirmation(): void
    {
        $response = $this->postJson('/api/users', [
            'username' => 'test',
            'phonenumber' => '+50688888888',
            'email' => 'test@email.com',
            'password' => 'testpassword',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.username', 'test');
        $this->assertDatabaseHas('users', ['email' => 'test@email.com']);
    }
}
