<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

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
}
