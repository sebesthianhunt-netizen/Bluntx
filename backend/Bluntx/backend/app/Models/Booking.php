<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'venue_id', 'snooker_table_id', 'start_time', 'end_time', 'total_amount', 'status', 'notes'
    ];
}
