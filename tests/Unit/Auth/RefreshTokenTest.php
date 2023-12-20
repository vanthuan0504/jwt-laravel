<?php

namespace Tests\Unit;

use Tests\TestCase;
use Database\Factories\UserFactory;
use App\Models\User;
use App\Models\Role;
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
        $userRole = Role::where('code', 'USER')->first();
        $user = User::factory()->create([
            "name" => "Refresh Token",
            "email" => 'refresh@gmail.com',
            "role_id" => $userRole->id

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
                        "name" => "Refresh Token",
                        "email" => "refresh@gmail.com"
                    ]
                ]   
        ]);
        $userToDelete = User::where('email', 'refresh@gmail.com')->first();
        
        if ($userToDelete) {
            $userToDelete->delete();
            echo 'Removed User.';
        } else {
            echo 'User does not exist.';
        }
    }

    public function testShouldSuccessfullyLoggedOut() 
    {
        $userRole = Role::where('code', 'USER')->first();
        $user = User::factory()->create([
            "name" => "Refresh Token",
            "email" => 'refresh@gmail.com',
            "role_id" => $userRole->id

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

        $userToDelete = User::where('email', 'refresh@gmail.com')->first();
        
        if ($userToDelete) {
            $userToDelete->delete();
            echo 'Removed User.';
        } else {
            echo 'User does not exist.';
        }
    }




}
