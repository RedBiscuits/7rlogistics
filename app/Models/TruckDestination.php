<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TruckDestination extends Model
{
    protected $fillable = ['to_area', 'from_area', 'truck_type_id', 'price'];

    protected $table = 'trucks_destinations';

    public function toArea()
    {
        return $this->belongsTo(Area::class, 'to_area');
    }

    public function fromArea()
    {
        return $this->belongsTo(Area::class, 'from_area');
    }

    public function truckType()
    {
        return $this->belongsTo(TruckType::class, 'truck_type_id');
    }

    public function destinations()
    {
        return $this->hasMany(Destination::class, 'truck_type_id');
    }
}
