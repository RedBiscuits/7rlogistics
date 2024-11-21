<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function fromDestinations()
    {
        return $this->hasMany(Destination::class, 'from_area');
    }

    public function toDestinations()
    {
        return $this->hasMany(Destination::class, 'to_area');
    }

    public function destinations()
    {
        return $this->hasMany(Destination::class, 'from_area')
            ->orWhere('to_area', $this->id);
    }
}
