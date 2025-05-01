<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Login API
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function register(Request $request)
    {
         $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'phone' => 'required|string|unique:users',
            'gender' => 'nullable|in:male,female,other',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $user = User::create([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'gender' => $validated['gender'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        // Generate token immediately
        $token = Auth::guard('api')->login($user);

        return $this->respondWithToken($token);

    }

    // Authenticated user info
    public function me()
    {
        return response()->json(Auth::user());
    }

    // Logout (invalidate token)
    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    // Refresh token
    public function refresh()
    {
        return $this->respondWithToken(Auth::refresh());
    }

    // Helper for token response
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }
}

