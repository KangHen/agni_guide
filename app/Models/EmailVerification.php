<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    protected $fillable = [
        'email',
        'token',
        'expired_at',
        'is_verified',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
    ];
}
