<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'challenger_id',
        'opponent_id',
        'venue_id',
        'stake_amount',
        'insurance_amount',
        'total_escrow',
        'status',
        'expires_at',
    ];
}
