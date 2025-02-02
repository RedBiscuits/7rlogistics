<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function fromArea()
    {
        return $this->belongsTo(Area::class, 'from_area');
    }

    public function toArea()
    {
        return $this->belongsTo(Area::class, 'to_area');
    }

    public function fromCity()
    {
        return $this->belongsTo(City::class, 'from_city');
    }

    public function toCity()
    {
        return $this->belongsTo(City::class, 'to_city');
    }

    public function truck()
    {
        return $this->belongsTo(TruckType::class, 'truck_type_id');
    }
}
