<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;

class UserApiTest extends TestCase
{
    use WithFaker;

    public function test_can_get_all_users()
    {
        User::factory()->count(3)->create();

        $response = $this->get('/users');

        $response->assertStatus(200)
                ->assertJsonCount(3);
    }

    public function test_can_create_user()
    {
        $userData = [
            'name' => 'Matheus Rodrigues',
            'email' => 'matheus.test.' . time() . '@email.com',
            'password' => 'test_password123'
        ];

        $response = $this->postJson('/users', $userData);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'name' => 'Matheus Rodrigues'
                    ],
                    'message' => 'User created successfully'
                ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Matheus Rodrigues'
        ]);
    }

    public function test_can_get_specific_user()
    {
        $user = User::factory()->create();

        $response = $this->get("/users/{$user->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email
                    ],
                    'message' => 'User retrieved successfully'
                ]);
    }

    public function test_can_update_user()
    {
        $user = User::factory()->create();
        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated.test.' . time() . '@example.com'
        ];

        $response = $this->putJson("/users/{$user->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'name' => 'Updated Name'
                    ],
                    'message' => 'User updated successfully'
                ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name'
        ]);
    }

    public function test_can_delete_user()
    {
        $user = User::factory()->create();

        $response = $this->deleteJson("/users/{$user->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'User deleted successfully'
                ]);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_health_endpoint_returns_ok()
    {
        $response = $this->get('/health');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'status' => 'ok'
                    ],
                    'message' => 'Service is healthy'
                ]);
    }

    public function test_validation_requires_name_email_and_password()
    {
        $response = $this->postJson('/users', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_email_must_be_unique()
    {
        $uniqueEmail = 'unique.test.' . time() . '@example.com';
        User::factory()->create(['email' => $uniqueEmail]);

        $response = $this->postJson('/users', [
            'name' => 'Test User',
            'email' => $uniqueEmail,
            'password' => 'test_password123'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }
} 