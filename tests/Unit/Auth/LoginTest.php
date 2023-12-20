<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    public function testShouldReturnInvalidCredentials()
    {
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
            "password" => "Abc@12345"
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
                "message" => "Invalid data",
                'data' => [
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
                'message' => 'Logged in'
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
