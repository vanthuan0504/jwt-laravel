<?php

namespace Tests\Unit;

use Tests\TestCase;
use Database\Factories\UserFactory;
use App\Models\User;
use JWTAuth;

class RefreshTokenTest extends TestCase
{
    public function testShouldNotGetUserProfile()
    {
        $response = $this->post('/api/auth/refresh');

        $response->assertStatus(500);
        $response->assertSee('Route [login] not defined');

    }

    public function testShouldReturnUserHasRegainedAccessToken() 
    {
        $user = User::factory()->create([
            "name" => "Refresh Token",
        ]);
        // Simulate authentication by creating a token for the user
        // $token = $user->createToken('test-token')->plainTextToken;

        // Create a token for the user using JWTAuth
        $token = JWTAuth::fromUser($user);

        // Make a request to the authenticated route
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/auth/refresh');

        $response->assertStatus(200)
            ->assertJson([
                "data" => [
                    "user" => [
                        "name" => "Refresh Token"
                    ]
                ]   
        ]);
    }

    public function testShouldSuccessfullyLoggedOut() 
    {
        $user = User::factory()->create([
            "name" => "Refresh Token",
        ]);
        // Simulate authentication by creating a token for the user
        // $token = $user->createToken('test-token')->plainTextToken;

        // Create a token for the user using JWTAuth
        $token = JWTAuth::fromUser($user);

        // Make a request to the authenticated route
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                "status" => true,
                "message" => "User successfully signed out"  
        ]);
    }




}
