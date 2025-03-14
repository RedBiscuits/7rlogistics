<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mission extends Model
{
    protected $fillable = [
        'shipment_id',
        'driver_id',
        'truck_id',
        'from_area',
        'to_area',
        'date',
        'time',
        'status',
        'note',
        'price',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function truck()
    {
        return $this->belongsTo(Truck::class);
    }

    public function fromArea()
    {
        return $this->belongsTo(Area::class, 'from_area');
    }

    public function toArea()
    {
        return $this->belongsTo(Area::class, 'to_area');
    }
}
