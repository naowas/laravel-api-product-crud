<?php

namespace App\Http\Controllers;

use App\Models\User;
use Hash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function login(Request $request): Response|Application|ResponseFactory
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);


        // Check email
        $user = User::where('email', $fields['email'])->first();

        // check password

        if(!$user || !Hash::check($fields['password'], $user->password)){
            return  response([
                'message'=> 'Wrong credentials'
            ],401);
        }

        $token = $user->createToken('thisapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response($response, 201);
    }

    public function register(Request $request): Response|Application|ResponseFactory
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed',
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
        ]);

        $token = $user->createToken('thisapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response($response, 201);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged out. Token destroyed'
        ];
    }
}
