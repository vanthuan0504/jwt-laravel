<?php

namespace Tests\Unit;

use Tests\TestCase;
use Database\Factories\UserFactory;
use App\Models\User;
use JWTAuth;

class UserProfileTest extends TestCase
{
    public function testShouldNotGetUserProfile()
    {
        $response = $this->get('/api/auth/user-profile');

        $response->assertStatus(500);
        $response->assertSee('Route [login] not defined');

    }

    public function testShouldReturnUserProfile() 
    {
        $response = $this->get('/api/auth/user-profile');
        $user = User::factory()->create([
            "name" => "Test ABC",
        ]);
        // Simulate authentication by creating a token for the user
        // $token = $user->createToken('test-token')->plainTextToken;

        // Create a token for the user using JWTAuth
        $token = JWTAuth::fromUser($user);

        // Make a request to the authenticated route
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/auth/user-profile');

        $response->assertStatus(200)
            ->assertJson([
                'name' =>  "Test ABC"
        ]);
    }

}
