<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TruckName extends Model
{
    protected $table = 'trucks_names';

    protected $fillable = ['name'];

    protected function trucks()
    {
        return $this->hasMany(Truck::class, 'name_id');
    }
}
