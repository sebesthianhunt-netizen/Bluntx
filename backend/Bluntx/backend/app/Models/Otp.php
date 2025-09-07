<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone', 'code', 'purpose', 'expires_at', 'attempts'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}


