<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function insertUser(Request $request)
    {
try {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_initial' => ['nullable', 'string', 'max:1', 'regex:/^[a-zA-Z]$/'],
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile_number' => 'required|string|max:20|unique:users,mobile_number',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,employer,employee',
        ], [
            // validation messages...
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'middle_initial' => $validated['middle_initial'] ?? null,
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'mobile_number' => $validated['mobile_number'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        // 🔔 Send verification email
        $user->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => 'User created successfully. Verification email sent.',
            'user' => $user,
        ], 201);

    } catch (ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors(),
        ], 422);
    }
    }

public function getUserDetails(Request $request)
{
   $user = $request->user()->load('companies');


return response()->json([
    'success' => true,
    'user' => $user,
]);

}


    public function getAllUsers()
    {
        $users = User::all();

        return response()->json([
            'success' => true,
            'users' => $users,
        ]);
    }

}
