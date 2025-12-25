<?php

namespace App\Http\Controllers\Frontend;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController
{
    public function login(Request $request)
    {
        $request->validate([
            'eth_address' => 'required|string'
        ]);

        $user = User::firstOrCreate([
            'eth_address' => $request->eth_address
        ]);

        $token = $user->createToken('frontend-token', ['frontend'])->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    public function dashboard(Request $request)
    {
        return response()->json([
            'message' => 'Frontend user authenticated',
            'user' => $request->user()
        ]);
    }

    public function logout(Request $request)
    {
        // Revoke current access token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Frontend user logged out successfully'
        ]);
    }
}
