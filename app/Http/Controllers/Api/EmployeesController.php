<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

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
                'status' => true,
                "message" => "Employee updated successfully"
            ]);
        } else {
            return response()->json([
                'status' => false,
                "message" => "Employee not found."
            ], 404);
        }
    }
    
    public function store(Request $request) {
        $validateUser = Validator::make($request->all(),
        [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'username' => 'required',
            'surname' => 'required|string|max:255'
        ]);

        if($validateUser->fails()){
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validateUser->errors()
            ], 401);
        }

        $user = new User();
        $user->name = $request->input('name');
        $user->surname = $request->input('surname');
        $user->email = $request->input('email');
        $user->username = $request->input('username');
        $user->password = bcrypt($request->input('password'));
        $user->role = 'employee';
        $user->save();
        return response()->json([
            'status' => true,
            "message" => "Employee created successfully"
        ], 201);
    }

    public function destroy($id) {
        $user = User::where('role', 'employee')->find($id);
        if ($user){
            $user->delete();
            return response()->json([
                'status' => true,
                "message" => "Employee deleted successfully"
            ]);
        } else {
            return response()->json([
                'status' => false,
                "message" => "Employee not found."
            ], 404);
        }
    }
}