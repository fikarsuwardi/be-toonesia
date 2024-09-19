<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Cache;


class ProfileController extends Controller
{
    use ValidatesRequests;
    // Get the authenticated user's profile

    public function show()
    {
        $user = Auth::user(); // Get the authenticated user

        // Cache key based on the user's ID
        $cacheKey = 'profile_' . $user->id;

        // Try to get the profile from cache, or fetch from DB and cache it for 60 minutes
        $profile = Cache::remember($cacheKey, 60 * 60, function () use ($user) {
            return $user->profile;
        });

        // If no profile is found in the database
        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        return response()->json($profile, 200);
    }


    // Create a new profile for the authenticated user
    public function store(Request $request)
    {
        Log::info('Creating profile with data: ', $request->all());

        // Validate request data
        $this->validate($request, [
            'full_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'marital_status' => 'required|in:single,married',
        ]);

        // Check if the authenticated user already has a profile
        if (Auth::user()->profile) {
            Log::info('User already has a profile: ', ['user_id' => Auth::id()]);
            return response()->json(['message' => 'Profile already exists'], 400);
        }

        // Create the profile for the authenticated user
        $profile = Profile::create([
            'user_id' => Auth::id(),
            'full_name' => $request->full_name,
            'address' => $request->address,
            'gender' => $request->gender,
            'marital_status' => $request->marital_status,
        ]);

        Log::info('Profile created successfully: ', $profile->toArray());

        return response()->json($profile, 201);
    }


    // Update the authenticated user's profile
    public function update(Request $request)
    {
        $profile = Auth::user()->profile;

        // Check if the profile exists
        if (!$profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        // Validate request data
        $this->validate($request, [
            'full_name' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string|max:255',
            'gender' => 'sometimes|required|in:male,female',
            'marital_status' => 'sometimes|required|in:single,married',
        ]);

        // Update the profile with new data
        $profile->update($request->all());

        // Clear the cache for this profile
        Cache::forget('profile_' . Auth::id());

        return response()->json($profile, 200);
    }


    // Delete the authenticated user's profile
    public function destroy()
    {
        $profile = Auth::user()->profile;

        if (!$profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        // Delete the profile
        $profile->delete();

        // Clear the cache for this profile
        Cache::forget('profile_' . Auth::id());

        return response()->json(['message' => 'Profile deleted successfully'], 200);
    }
}
