<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
    // Register a new user
    public function register(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Create a new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Generate a JWT token for the new user
        $token = JWTAuth::fromUser($user);

        // Return the token in the response
        return response()->json([
            'token' => $token,
        ], 201);
    }

    // Login user and return the JWT token
    public function login(Request $request)
    {
        try {
            // Get the credentials from the request
            $credentials = $request->only('email', 'password');

            // Log the credentials being passed for debugging purposes
            Log::info('Credentials:', $credentials);

            // Now, attempt to authenticate the user
            if (!$token = JWTAuth::attempt($credentials)) {
                Log::error('Invalid credentials for email: ' . $credentials['email']);
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

            // If login is successful, return the token
            Log::info('Login successful for email: ' . $credentials['email']);
            return response()->json(['token' => $token]);
        } catch (\Exception $e) {
            // Log any exception that occurs during login
            Log::error('Exception during login: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred during login.'], 500);
        }
    }


    // Log the user out (invalidate the token)
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'User successfully logged out']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to log out, please try again'], 500);
        }
    }

    // Get the authenticated user details
    public function me()
    {
        return response()->json(Auth::user());
    }
}
