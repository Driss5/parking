<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Reservation;

class Parking extends Model
{
    
    protected $fillable = [
        'name',
        'location',
        'tariffs',
        'capacity',
        'available_spots',
        'image',
        'description',
        'number',
        'status',
        'agent_id'
    ];

    public function agent() {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function reservations() {
        return $this->hasMany(Reservation::class);
    }
}
