<?php

namespace Flat3\Lodata\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    public function passengers()
    {
        return $this->hasMany(Passenger::class);
    }

    public function originAirport()
    {
        return $this->hasOne(Airport::class, 'code', 'origin');
    }

    public function destinationAirport()
    {
        return $this->hasOne(Airport::class, 'code', 'destination');
    }
}

