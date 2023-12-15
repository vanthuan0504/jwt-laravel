<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 422);
        }
        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }
        return response()->json([
            'status' => true,
            'message' => 'User has regained acccess token',
            'data' => $this->createNewtoken($token)
        ], 200);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        }
        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));
        return response()->json([
            'status' => true,
            'message' => 'User successfully registered',
            'users' => $user
        ], 201);
    }

    public function logout()
    {
        auth()->logout();
        return response()->json([
            'status' => true,
            'message' => 'User successfully signed out'
        ], 200);
    }

    public function refresh()
    {
        return response()->json([
            'status' => true,
            'message' => 'User has regained acccess token',
            'data' => $this->createNewtoken(auth()->refresh())
        ], 200);
    }

    public function userProfile()
    {
        return response()->json(auth()->user());
    }

    protected function createNewtoken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ];
    }
}
