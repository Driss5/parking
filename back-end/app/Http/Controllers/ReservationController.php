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

    public function agentReservations() {
        $user = auth()->user();

        if ($user->role !== 'agent') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $reservations = Reservation::whereHas('parking', function($query) use ($user) {
            $query->where('agent_id', $user->id);
        })->get();

        return response()->json(['reservations' => $reservations], 200);
    }

    public function updateReservationStatus(Request $request, $id, $status) {
        $user = auth()->user();

        if ($user->role !== 'agent') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $reservation = Reservation::find($id);

        if (!$reservation) {
            return response()->json(['message' => 'Reservation not found'], 404);
        }

        $reservation->status = $status;
        $reservation->save();

        return response()->json(['message' => 'Reservation status updated', 'reservation' => $reservation], 200);
    }

    public function agentDashboard() {
        $user = auth()->user();
        $parking = Parking::where('agent_id', $user->id)->first();

        if ($user->role !== 'agent') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $totalReservations = Reservation::whereHas('parking', function($query) use ($user) {
            $query->where('agent_id', $user->id);
        })->count();

        $pendingReservations = Reservation::whereHas('parking', function($query) use ($user) {
            $query->where('agent_id', $user->id);
        })->where('status', 'pending')->count();

        $completedReservations = Reservation::whereHas('parking', function($query) use ($user) {
            $query->where('agent_id', $user->id);
        })->where('status', 'confirmed')->count();

        return response()->json([
            'total_reservations' => $totalReservations,
            'pending_reservations' => $pendingReservations,
            'completed_reservations' => $completedReservations,
            'available_spots' => $parking ? $parking->available_spots : 0
        ], 200);
    }
}
