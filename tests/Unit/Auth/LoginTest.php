<?php

namespace Tests\Unit;

use Tests\TestCase;
use Database\Factories\UserFactory;
use App\Models\User;
use App\Models\Role;

class LoginTest extends TestCase
{
    public function testShouldReturnInvalidCredentials()
    {
        // Create role
        foreach ([
            ['name' => 'Admin', 'code' => 'ADMIN'],
            ['name' => 'Supervisor', 'code' => 'SUPERVISOR'],
            ['name' => 'Staff', 'code' => 'STAFF'],
            ['name' => 'User', 'code' => 'USER']
        ] as $roleData) {
            Role::firstOrCreate($roleData);
        }

        $userRole = Role::where('code', 'USER')->first();

        // Create user
        $userToDelete = User::where('email', 'login@gmail.com')->first();
        
        if ($userToDelete) {
            $userToDelete->delete();
            echo 'Removed User.';
        } else {
            echo 'User does not exist.';
        }
        $user = User::factory()->create([
            "email" => "login@gmail.com",
            "password" => "Abc@12345",
            "role_id" => $userRole->id
        ]);

        // Assert that the user is present in the database
        $this->assertDatabaseHas('users', ['email' => 'login@gmail.com']);

        $response = $this->postJson('/api/auth/login', [
            "email" => "login1@gmail.com",
            "password" => "Abc@12345",
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'status' => false,
                'message' => "Invalid credentials"
            ]);

        $response1 = $this->postJson('/api/auth/login', [
            "email" => "login@gmail.com",
            "password" => "Abc@123456",
        ]);

        $response1->assertStatus(401)
            ->assertJson([
                'status' => false,
                'message' => "Invalid credentials"
            ]);

        
    }

    public function testShouldReturnTheFieldEmailIsRequire() {
        $response = $this->postJson('/api/auth/login', [
                "password" => "Abc@12345"
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => false,
                'message' => [
                    "email" => [
                        "The email field is required."
                    ]
                ]
        ]);
    }

    public function testShouldReturnUserLoggedIn() {
        $response = $this->postJson('/api/auth/login', [
                "email" => "login@gmail.com",
                "password" => "Abc@12345"
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'User logged in'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                "data" => [
                    "token_type" => "bearer",
                    "expires_in" => 3600
                ]
        ]);
        $userToDelete = User::where('email', 'login@gmail.com')->first();
        
        if ($userToDelete) {
            $userToDelete->delete();
            echo 'Removed User.';
        } else {
            echo 'User does not exist.';
        }
    }

}
