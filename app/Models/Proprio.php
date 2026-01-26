<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;


class Proprio extends Authenticatable
{
    protected $table = 'proprio';

    protected $fillable = [
        'nom',
        'prenom',
        'numero',
        'password'
    ];

    protected $hidden = [
        'password'
    ];
}
