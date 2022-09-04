<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Mockery\Matcher\Type;

class AuthController extends Controller
{
    public function register(request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        // Create user
            $user = User::create([
            'name'=> $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        //Create Token
        $token = $user->createToken('ilovecat')->plainTextToken;

        $response = [
        'user' => $user
        ,'token' =>$token
        ];

        return response($response, 201);
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        /*
            select eamil from user where email = "myemail@email.com"
        */
        $user = User::where('email',$fields['email'])->first();

        if (! $user || ! Hash::check($fields['password'],$user->password))
        {
            return response([
                'message' => 'eamil or password not correct'
            ],401);
        }

        //create Token
        $token = $user->createToken('ilovecat')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];
        return response($response, 201);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return ['message'=> 'Logged out'];
    }
}