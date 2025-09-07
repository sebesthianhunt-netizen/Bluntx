<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider', 'reference', 'event', 'signature', 'status', 'headers', 'payload'
    ];

    protected $casts = [
        'headers' => 'array',
        'payload' => 'array',
    ];
}


