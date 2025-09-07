<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'venue_id', 'tournament_type', 'entry_fee', 'prize_pool', 'max_participants', 'status'
    ];
}
