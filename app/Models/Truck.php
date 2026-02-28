<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Truck extends Model
{
    use HasFactory, \App\Models\Traits\RecordsModelChangeNotifications;

    protected $guarded = [];

    public function truckType()
    {
        return $this->belongsTo(TruckType::class, 'truck_type_id');
    }

    public function truckDestination()
    {
        return $this->hasMany(TruckDestination::class, 'truck_id');
    }
}
