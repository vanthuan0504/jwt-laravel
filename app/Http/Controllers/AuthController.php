<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Schema(
 *     schema="UserSchema",
 *     title="User",
 *     description="User model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="johndoe@gmail.com"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * ),
 *
 * @OA\Schema(
 *     schema="UserSignUp",
 *     title="User sign up",
 *     description="The information for signing up an user",
 *     required={"name", "email", "password", "password_confirmation"},
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="password", type="string", format="password", example="password123"),
 *     @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
 * ),
 * @OA\Schema(
 *     schema="UserSignIn",
 *     title="User sign in",
 *     description="The information for signing in an user",
 *     required={"email", "password"},
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="password", type="string", format="password", example="password123")
 * ),
 * @OA\Schema(
 *     schema="UserTokenDataReponse",
 *     title="User token data response",
 *     description="The information containing token and token metadata",
 *     @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGc..."),
 *     @OA\Property(property="token_type", type="string", example="bearer"),
 *     @OA\Property(property="expires_in", type="number", example=3600),
 *     @OA\Property(property="user", type="object")
 * )
 */
class AuthController extends Controller
{

    /**
     * @OA\Post(
     *      path="/api/auth/login",
     *      operationId="login",
     *      tags={"Authentication"},
     *      summary="Login a user",
     *      description="Log in a user with the provided email, and password.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  ref="#/components/schemas/UserSignIn"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="User successfully logged in",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="User successfully logged in"),
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/UserTokenDataReponse"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Invalid credentials",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Invalid credentials."),
     *          )
     *      ),
     * )
     */

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::jsonResponse(
                false, 'Invalid data', 
                $validator->errors(), 
                400);
        }

        if (!$token = auth()->attempt($validator->validated())) {
            return ResponseHelper::jsonResponse(false, 'Invalid credentials', [], 401);
        }

        return ResponseHelper::jsonResponse(
            true, 
            'Logged in', 
            $this->createNewToken($token), 
            200);
    }

    /**
     * @OA\Post(
     *      path="/api/auth/register",
     *      operationId="register",
     *      tags={"Authentication"},
     *      summary="Register a new user",
     *      description="Create a new user with the provided name, email, and password.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  ref="#/components/schemas/UserSignUp"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="User successfully registered",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="User successfully registered"),
     *              @OA\Property(property="user", type="object", ref="#/components/schemas/UserSchema"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *          )
     *      ),
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6'
        ]);

        if ($validator->failed()) {
            return ResponseHelper::jsonResponse(
                false,
                'Invalid data',
                $validator->errors(),
                400
            );
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return ResponseHelper::jsonResponse(true, 'Registered', $user, 201);
    }

    public function getUserProfile(Request $request)
    {
        if ($request->user()->cannot('view', auth()->user())) {
            return ResponseHelper::jsonResponse(false, 'Unauthorization', [], 403); 
        }
        return ResponseHelper::jsonResponse(true, '', auth()->user(), 200);
        
    }

    public function updateUserProfile(Request $request)
    {

        if ($request->user()->cannot('update', auth()->user())) { 
            return ResponseHelper::jsonResponse(true, 'Unauthorization', [], 403);
            
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return ResponseHelper::jsonResponse(false, 'Invalid data', [], 400);
        }

        $user = User::find(auth()->user()->id);
        if (!$user) {
            return ResponseHelper::jsonResponse(false, 'User not found', [], 404);
        }
        $user->update(['name' => $request->name]);

        return ResponseHelper::jsonResponse(true, 'User information updated', $user, 200);
    }

    public function changePassword(Request $request)
    {
        if ($request->user()->cannot('update', auth()->user())) {
            return ResponseHelper::jsonResponse(false, 'Unauthorization', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return ResponseHelper::jsonResponse(false, 'Invalid data', [], 400);
        }

        $userId = auth()->user()->id;
        $user = User::where('id', $userId)->update(
            ['password' => bcrypt($request->new_password)]
        );

        return ResponseHelper::jsonResponse(false, 'User successfully changed password', $user, 200);
    }

    public function logout()
    {
        auth('api')->logout(true);
        return ResponseHelper::jsonResponse(true, 'Logged out', [], 204);
    }

    public function refresh()
    {
        return ResponseHelper::jsonResponse(
            true,
            'Regained acccess token',
            $this->createNewToken(auth()->refresh()),
            200
        );
    }

    protected function createNewToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ];
    }
}
