<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    /**
     * Create User
     * @param Request $request
     * @return User
     */
    public function createUser(Request $request) {
        try{
            
            $validateUser = Validator::make($request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
                'username' => 'required|string|max:255|unique:users',
                'surname' => 'required|string|max:255'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'username' => $request->username,
                'surname' => $request->surname
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'token' =>$user->createToken("API TOKEN")->plainTextToken
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * login User
     * @param Request $request
     * @return User
     */
    public function loginUser(Request $request) {
        try{
            $validateUser = Validator::make($request->all(),
            [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if(!Auth::attempt($request->only(['email','password']))){
                return response()->json([
                    'status' => true,
                    'message' => 'Email & Password does not match with our record.',
                ],401);
            }

            $user = User::where('email', $request->email)->first();
            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'token' =>$user->createToken("API TOKEN")->plainTextToken
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

}
