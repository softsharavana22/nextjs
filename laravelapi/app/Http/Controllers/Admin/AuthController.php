<?php

namespace App\Http\Controllers\Admin;

use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'pattern' => 'required|digits:5'
        ]);

        $admin = AdminUser::where('email', $request->email)->first();

        if (
            !$admin ||
            !Hash::check($request->password, $admin->password) ||
            $admin->pattern !== $request->pattern
        ) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $admin->createToken('admin-token', ['admin'])->plainTextToken;

        return response()->json([
            'token' => $token,
            'admin' => $admin
        ]);
    }

    public function dashboard(Request $request)
    {
        return response()->json([
            'message' => 'Admin authenticated',
            'admin' => $request->user()
        ]);
    }

    public function logout(Request $request)
    {
        // Revoke current access token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Admin logged out successfully'
        ]);
    }

}
