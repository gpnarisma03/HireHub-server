<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
public function login(Request $request)
{
    // Validate input
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    // Check if user exists
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Email does not exist',
        ], 401);
    }

    // Check password
    if (!Hash::check($request->password, $user->password)) {
        return response()->json([
            'success' => false,
            'message' => 'Password is incorrect',
        ], 401);
    }

    // ✅ Check if email is verified
    if (is_null($user->email_verified_at)) {
        return response()->json([
            'success' => false,
            'message' => 'Please verify your email address before logging in.',
        ], 403); // Forbidden
    }

    // Login success
    return response()->json([
        'success' => true,
        'message' => 'Login successful',
        'access_token' => $user->createToken('auth_token')->plainTextToken,
'user' => [
    'user_id' => $user->user_id
],
        'token_type' => 'Bearer',
    ]);
}

}
