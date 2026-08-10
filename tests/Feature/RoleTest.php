<?php

namespace Tests\Feature;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_roles_index_returns_a_json_collection(): void
    {
        Role::create([
            'name' => 'Admin',
            'description' => 'Administrative access',
        ]);

        $response = $this->getJson('/api/roles');

        $response->assertOk();
        $response->assertJsonFragment([
            'name' => 'Admin',
            'description' => 'Administrative access',
        ]);
    }

    public function test_roles_store_creates_a_role(): void
    {
        $response = $this->postJson('/api/roles', [
            'name' => 'Viewer',
            'description' => 'Read-only access',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('roles', [
            'name' => 'Viewer',
            'description' => 'Read-only access',
        ]);
    }
}
