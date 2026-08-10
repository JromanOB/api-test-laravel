<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_roles_route_is_registered_under_users_namespace(): void
    {
        $routes = $this->app['router']->getRoutes();

        $routeFound = false;

        foreach ($routes as $route) {
            if ($route->uri() === 'api/users/add-roles/{id}' && in_array('POST', $route->methods(), true)) {
                $routeFound = true;
                break;
            }
        }

        $this->assertTrue($routeFound, 'Expected POST /api/users/add-roles/{id} to be registered.');
    }

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
