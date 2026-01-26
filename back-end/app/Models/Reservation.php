<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Parking;

class Reservation extends Model
{

    protected $fillable = [
        'name',
        'cin',
        'number_phone',
        'car',
        'duration_minutes',
        'status',
        'user_id',
        'parking_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function parking() {
        return $this->belongsTo(Parking::class);
    }
}
