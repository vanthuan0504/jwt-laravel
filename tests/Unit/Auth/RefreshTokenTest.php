<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class RefreshTokenTest extends TestCase
{
    public function testShouldNotRegainAccessToken()
    {
        $response = $this->post('/api/auth/refresh');

        //$response->assertStatus(500);
        //$response->assertSee('Route [login] not defined');
        $response->assertStatus(401)
            ->assertJson([
                "status" => false,
                "message" => "Unauthentication"
        ]);

    }

    public function testShouldReturnUserHasRegainedAccessToken() 
    {
        $userToDelete = User::where('email', 'refresh@gmail.com')->first();
        
        if ($userToDelete) {
            $userToDelete->delete();
            echo 'Removed User.';
        } else {
            echo 'User does not exist.';
        }

        $user = User::factory()->create([
            "name" => "Refresh Token",
            "email" => 'refresh@gmail.com'
        ]);

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
                        "name" => "Refresh Token",
                        "email" => "refresh@gmail.com"
                    ]
                ]   
        ]);


    }

    public function testShouldSuccessfullyLoggedOut() 
    {
        $userToDelete = User::where('email', 'refresh@gmail.com')->first();
        
        if ($userToDelete) {
            $userToDelete->delete();
            echo 'Removed User.';
        } else {
            echo 'User does not exist.';
        }

        $user = User::factory()->create([
            "name" => "Refresh Token",
            "email" => 'refresh@gmail.com',
        ]);

        // Create a token for the user using JWTAuth
        $token = JWTAuth::fromUser($user);

        // Make a request to the authenticated route
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/auth/logout');

        $response->assertStatus(204);
    }



}
