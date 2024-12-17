<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Truck extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function destinations()
    {
        return $this->hasMany(Destination::class, 'truck_id');
    }

    // public function TruckName(){
    //     return $this->belongsTo(TruckName::class, 'name_id');
    // }
}
