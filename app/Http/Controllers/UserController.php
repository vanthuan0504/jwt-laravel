<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\ResponseHelper;

class UserController extends Controller
{
    public function createStaffUser(Request $request)
    {

    }

    public function getAllUsers(Request $request)
    {
        if ($request->user()->cannot('viewAny', User::class)) { // Admin and Supervisor can viewAny user records
            return ResponseHelper::jsonResponse(false, 'Unauthorization', [], 403);
        }
        $users = User::all();

        return ResponseHelper::jsonResponse(true, '', $users, 200);
    }

}
