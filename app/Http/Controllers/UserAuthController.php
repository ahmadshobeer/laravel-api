<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class UserAuthController extends Controller
{
    //

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(),422);
        }
        $user = User::create([
            // 'name' => $registerUserData['name'],
            // 'email' => $registerUserData['email'],
            // 'password' => Hash::make($registerUserData['password']),
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    
            return response()->json([
                'success'=>true,
                'message' => 'User registered successfully ',
            ]);
        
       
    }

  
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'success'=>false,
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([ 'success'=>true,
        'user'=>$user,
        'access_token' => $token, 'token_type' => 'Bearer']);
    }

    public function logout(Request $request)
    {
        $logout = $request->user()->currentAccessToken()->delete();

        if($logout){
            return response()->json(['success'=>true,'message' => 'Successfully logged out']);
        }else{

            return response()->json(['success'=>false,'message' => 'Logout failed, try again']);
        }
    }

    // Detail User
    public function userDetail(Request $request)
    {
        return response()->json($request->user());
    }
}
