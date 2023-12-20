<?php

namespace Tests\Unit;

use Tests\TestCase;
use Database\Factories\UserFactory;
use App\Models\User;

class RegisterTest extends TestCase
{
    public function testShouldReturnPassowrdDidNotMatch()
    {
        $response = $this->postJson('/api/auth/register', [
            "name" => "Laravel",
            "email" => "register@gmail.com",
            "password" => "Abc@12345",
            "password_confirmation" => "Abc@123456"
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => false,
                'message' => [
                    "password" => [
                        "The password field confirmation does not match."
                    ]
                ]
            ]);
    }

    public function testShouldReturnTheFieldNameIsRequire()
    {
        $response = $this->postJson('/api/auth/register', [
            "email" => "register@gmail.com",
            "password" => "Abc@12345",
            "password_confirmation" => "Abc@12345"
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => false,
                'message' => [
                    "name" => [
                        "The name field is required."
                    ]
                ]
            ]);
    }

    public function testShouldReturnUserSuccessfullyRegistered()
    {
        $response = $this->postJson('/api/auth/register', [
            "name" => "Test",
            "email" => "register@gmail.com",
            "password" => "Abc@12345",
            "password_confirmation" => "Abc@12345"
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => true,
                'message' => "User successfully registered"
            ]);
            
        $userToDelete = User::where('email', 'register@gmail.com')->first();
        
        if ($userToDelete) {
            $userToDelete->delete();
            echo 'Removed User.';
        } else {
            echo 'User does not exist.';
        }
    }

    public function testShouldReturnEmailIsAlreadyTaken()
    {

        // First, create a user with the given email
        $user = User::factory()->create([
            'email' => 'register1@gmail.com',
        ]);

        // Assert that the user is present in the database
        $this->assertDatabaseHas('users', ['email' => 'register1@gmail.com']);

        $response = $this->postJson('/api/auth/register', [
            "name" => "Test",
            "email" => "register1@gmail.com",
            "password" => "Abc@12345",
            "password_confirma tion" => "Abc@12345"
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => false,
                'message' => [
                    "email" => [
                        "The email has already been taken."
                    ]
                ]
            ]);
        User::destroy($user->id);
    }
}
