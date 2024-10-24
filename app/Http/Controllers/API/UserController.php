<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:Admin'])->except([]);
    }

    public function index()
    {
        // List all users
        $users = User::all();
        return response()->json($users);
    }

    public function show($id)
    {
        // Show a single user
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user);
    }
}
