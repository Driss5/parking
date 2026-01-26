<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parking;
use App\Models\User;

class ParkingController extends Controller
{

    public function index() {
        $parking = Parking::all();
        return response()->json($parking, 200);
    }

    public function create(Request $request) {

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'tariffs' => 'required|numeric',
            'capacity' => 'required|integer',
            'available_spots' => 'required|integer',
            'image' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'number' => 'required|string|max:50',
            'status' => 'required|string|max:50',
            'agent_id' => 'required|exists:users,id',
        ]);
        $parking = Parking::create($validatedData);

        return response()->json(['message' => 'Parking created successfully', 'parking' => $parking], 201);
    }

    public function show($id) {
        $parking = Parking::find($id);

        if (!$parking) {
            return response()->json(['message' => 'Parking not found'], 404);
        }

        return response()->json($parking, 200);
    }

    public function update(Request $request, $id) {

        $parking = Parking::find($id);
        if (!$parking) {
            return response()->json(['message' => 'Parking not found'], 404);
        }

        $validatedData = $request->validate([
            'name'            => 'sometimes|string|max:255',
            'location'        => 'sometimes|string|max:255',
            'tariffs'         => 'sometimes|numeric',
            'capacity'        => 'sometimes|integer',
            'available_spots' => 'sometimes|integer',
            'image'           => 'nullable|string|max:255',
            'description'     => 'nullable|string',
            'number'          => 'sometimes|string|max:50',
            'status'          => 'sometimes|string|max:50',
            'agent_id'        => 'sometimes|exists:users,id',
        ]);

        $parking->update($validatedData);

        return response()->json([
            'message' => 'Parking updated successfully',
            'parking' => $parking
        ], 200);
    }

    public function delete($id) {
        $parking = Parking::find($id);
        if (!$parking) {
            return response()->json(['message' => 'Parking not found'], 404);
        }

        $parking->delete();

        return response()->json(['message' => 'Parking deleted successfully'], 200);
    }

    public function createUser(Request $request) {

        // $role = $request->input('role', 'user');

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
            'role' => 'agent',
        ]);

        return response()->json([
            'message' => 'User registered successfully', 
            'user' => $user,
        ], 201);
    }

    public function parkings() {
        $parkings = Parking::all();
        return response()->json($parkings, 200);
    }

}
