<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;

class AuthController extends Controller
{
    public function register(Request $request) {

        $role = $request->input('role', 'user');

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'city' => 'required|string|max:255',
            'number_phone' => 'required|string|max:20',
            'cin' => 'required|string|max:20|unique:users',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'city' => $validatedData['city'],
            'number_phone' => $validatedData['number_phone'],
            'cin' => $validatedData['cin'],
            'role' => $role,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully', 
            'user' => $user,
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ], 201);
    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth()->attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Sm7naa khoyaa, siir dir compte o aji'], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Mar7baaa biiiik', 
            'user' => $user,
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ], 200);
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'You have been logged out'], 200);
    }

    public function user(Request $request) {
        $user = $request->user();
        $reservations = Reservation::where('user_id', $user->id)->with('parking')->get();

        if ($reservations->isEmpty()) {
            return response()->json(['message' => 'No reservations found for this user', 'user' => $user], 200);
        } else {
            return response()->json(['user' => $user, 'reservations' => $reservations], 200);
        }
    }
    
}
