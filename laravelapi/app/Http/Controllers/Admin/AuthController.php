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

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password'     => 'required|string',
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);

        $admin = $request->user();

        // 🔐 Check old password
        if (!Hash::check($request->old_password, $admin->password)) {
            return response()->json([
                'status'  => 422,
                'message' => 'Old password is incorrect'
            ], 422);
        }

        // 🚫 Prevent same password reuse
        if (Hash::check($request->new_password, $admin->password)) {
            return response()->json([
                'status'  => 422,
                'message' => 'New password cannot be the same as old password'
            ], 422);
        }

        // ✅ Update password
        $admin->password = Hash::make($request->new_password);
        $admin->save();

        // 🔒 OPTIONAL: Revoke all tokens (force re-login)
        $admin->tokens()->delete();

        return response()->json([
            'status'  => 200,
            'message' => 'Password changed successfully. Please login again.'
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
