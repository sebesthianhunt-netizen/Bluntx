<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'type', 'status', 'amount', 'currency', 'provider', 'reference', 'meta'
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}
