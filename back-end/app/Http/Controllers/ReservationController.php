<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Reservation;
use App\Models\Parking;

class ReservationController extends Controller
{
    public function reserverParking(Request $request) {
        $parking_id = $request->input('parking_id');
        $user = auth()->user();

        $parking = Parking::find($parking_id);

        if ($parking && $parking->available_spots > 0) {
            $reservation = new Reservation();
            $reservation->user_id = $user->id;
            $reservation->parking_id = $parking->id;
            $reservation->name = $request->input('name');
            $reservation->cin = $request->input('cin');
            $reservation->number_phone = $request->input('number_phone');
            $reservation->car = $request->input('car');
            $reservation->duration_minutes = $request->input('duration_minutes');
            $reservation->status = 'pending';
            $reservation->save();

            $parking->available_spots -= 1;
            $parking->save();

            return response()->json(['message' => 'Parking reserved successfully', 'reservation' => $reservation], 201);
        } else {
            return response()->json(['message' => 'No available spots or parking not found'], 404);
        }
    }
}
