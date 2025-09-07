<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SnookerTable extends Model
{
    use HasFactory;

    protected $table = 'snooker_tables';

    protected $fillable = [
        'venue_id',
        'table_number',
        'table_type',
        'hourly_rate',
    ];
}
