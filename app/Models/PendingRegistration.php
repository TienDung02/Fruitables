<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingRegistration extends Model
{
    protected $fillable = [
        'email',
        'username',
        'password',
        'token',
        'step',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
