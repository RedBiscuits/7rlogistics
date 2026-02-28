<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory, \App\Models\Traits\RecordsModelChangeNotifications;

    protected $guarded = [];

    public function areas()
    {
        return $this->hasMany(Area::class, 'city_id');
    }
}
