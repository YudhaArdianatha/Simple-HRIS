<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // public function login(Request $request)
    // {
    //     $credentials = $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required|string'
    //     ]);

    //     if (!Auth::attempt($credentials)){
    //         return response()->json([
    //             'message' => 'Invalid login details',
    //         ], 401);
    //     }

    //     $user = Auth::user();
    //     $token = $user->createToken('api_token')->plainTextToken;

    //     return response()->json([
    //         'message' => 'Login successful',
    //         'token' => $token,
    //         'user' => $user->load('roles')
    //     ], 200);
    // }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        // Gunakan Auth::attempt() tanpa guard sanctum
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid login details',
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user->load(['roles' => function($query) {
                $query->where('guard_name', 'sanctum');
            }])
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful'
        ], 200);
    }
}
