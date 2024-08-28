<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AdminsController extends Controller
{
    public function index() {
        $users = User::where('role', 'admin')->get();
        return response()->json($users);
    }

    public function store(Request $request) {
        $request->validate([
            'username' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required',
        ]);

        $user = User::create([
            'username' => $request->username,
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'admin',
        ]);

        return response()->json([
            'status' => true,
            "message" => "Admin created successfully"
        ], 201);
    }
}
