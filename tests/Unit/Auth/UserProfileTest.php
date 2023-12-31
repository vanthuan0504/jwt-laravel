<?php

namespace Tests\Unit;

use Tests\TestCase;
use Database\Factories\UserFactory;
use App\Models\User;
use App\Models\Role;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserProfileTest extends TestCase
{
    public function testShouldNotReturnUserProfile()
    {
        $response = $this->get('/api/auth/me');

        $response->assertStatus(401)
            ->assertJson([
                'message' =>  "Unauthentication"
            ]);
    }

    public function testShouldReturnUserProfile()
    {
        $user = User::factory()->create([
            "name" => "User Role",
            "email" => 'user-role@gmail.com'

        ]);
        // Simulate authentication by creating a token for the user
        // $token = $user->createToken('test-token')->plainTextToken;

        // Create a token for the user using JWTAuth
        $token = JWTAuth::fromUser($user);

        // Make a request to the authenticated route
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/auth/me');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' =>  "User Role",
                    "email" => 'user-role@gmail.com'
                ]
            ]);

        User::destroy($user->id);
    }
}
