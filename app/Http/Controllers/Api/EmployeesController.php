<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class EmployeesController extends Controller
{
    public function index() {
        $users = User::where('role', 'employee')->get();
        return response()->json($users);
    }

    // public function showEmployee($id) {
    //     $user = User::where('role', 'employee')->find($id);
    //     if(!empty($user)){
    //         return response()->json($user);
    //     } else {
    //         return response()->json([
    //             "message" => "User not found."
    //         ], 404);
    //     }
    // } 

    public function updateEmployee(Request $request, $id) {
        $user = User::where('role', 'employee')->find($id);
        if ($user){
            $user->name = $request->input('name');
            $user->surname = $request->input('surname');
            $user->username = $request->input('username');
            $user->email = $request->input('email');
            $user->save();
            return response()->json([
                "message" => "Employee updated successfully"
            ]);
        } else {
            return response()->json([
                "message" => "Employee not found."
            ], 404);
        }
    }
    
    public function store(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|max:255|unique:users',
            'surname' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required'
        ]);
        $user = new User();
        $user->name = $request->input('name');
        $user->surname = $request->input('surname');
        $user->email = $request->input('email');
        $user->username = $request->input('username');
        $user->password = bcrypt($request->input('password'));
        $user->password_confirmation = $request->input('password_confirmation');
        $user->role = 'employee';
        $user->save();
        return response()->json([
            "message" => "Employee created successfully"
        ], 201);
    }

    public function destroy($id) {
        $user = User::where('role', 'employee')->find($id);
        if ($user){
            $user->delete();
            return response()->json([
                "message" => "Employee deleted successfully"
            ]);
        } else {
            return response()->json([
                "message" => "Employee not found."
            ], 404);
        }
    }
}