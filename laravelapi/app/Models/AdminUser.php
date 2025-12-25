<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class AdminUser extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'adminusers';

    protected $fillable = [
        'email',
        'password',
        'pattern'
    ];

    protected $hidden = ['password'];
}
