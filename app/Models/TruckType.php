<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\RecordsModelChangeNotifications;

class TruckType extends Model
{
    use RecordsModelChangeNotifications;

    protected $fillable = ['name', 'capacity', 'weight', 'length', 'width', 'height'];

    protected $table = 'truck_types';

    public function trucksDestinations()
    {
        return $this->hasMany(TruckDestination::class, 'truck_type_id');
    }

    public function trucks()
    {
        return $this->hasMany(Truck::class);
    }
}
