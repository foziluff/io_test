<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Driver extends Model
{
    use HasFactory;
    use HasApiTokens; //

    protected $fillable = [
        'first_name',
        'last_name',
        'login',
        'password',
    ];
}
