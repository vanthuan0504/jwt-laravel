<?php

namespace Tests\Unit;

use Tests\TestCase;
use Database\Factories\UserFactory;
use App\Models\User;

class LoginTest extends TestCase
{
    public function testShouldReturnInvalidCredentials()
    {

        // Assert that the user is present in the database
        $this->assertDatabaseHas('users', ['email' => 'abc@gmail.com']);

        $response = $this->postJson('/api/auth/login', [
            "email" => "abc1@gmail.com",
            "password" => "Abc@12345",
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'status' => false,
                'message' => "Invalid credentials"
            ]);

        $response1 = $this->postJson('/api/auth/login', [
            "email" => "abc@gmail.com",
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
                "email" => "abc@gmail.com",
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
    }

}
