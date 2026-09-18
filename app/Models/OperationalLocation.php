<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationalLocation extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'address',
        'maps_url',
        'coordinates',
        'phone',
    ];
}
