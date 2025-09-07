<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escrow extends Model
{
    use HasFactory;

    protected $fillable = [
        'initiator_id',
        'receiver_id',
        'amount',        // stored in kobo
        'currency',
        'asset',         // 'cash' (default)
        'status',        // pending, released, cancelled
        'reference',
        'note',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}


