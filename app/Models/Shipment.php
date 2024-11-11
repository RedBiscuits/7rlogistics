<?php

namespace App\Models;

use App\Enums\ShipmentStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Shipment extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia;

    protected $guarded = [];

    protected $casts = [
        'status' => ShipmentStatus::class,
        'pickup_date' => 'date:Y-m-d H:i',
    ];

    protected static function boot()
    {
        parent::boot();

        // Event handler for creating
        static::creating(function ($model) {
            // Remove an attribute and add another before creating
            if (isset($model->attributes['location'])) {
                $model->attributes['latitude'] = $model->attributes['location']['lat']; // Or compute the value dynamically
                $model->attributes['longitude'] = $model->attributes['location']['lng'];
                unset($model->attributes['location']);
            }
        });

        // Event handler for updating
        static::updating(function ($model) {
            // Remove an attribute and add another before updating
            if (isset($model->attributes['location'])) {
                $model->attributes['latitude'] = $model->attributes['location']['lat']; // Or compute the value dynamically
                $model->attributes['longitude'] = $model->attributes['location']['lng'];
                unset($model->attributes['location']);
            }
        });
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function truck()
    {
        return $this->belongsTo(Truck::class, 'truck_id');
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

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function claim()
    {
        return $this->hasOne(Claim::class);
    }

    // public function getLatitudeAttribute()
    // {
    //     return $this->location['lat'] ?? 1;
    // }

    // public function getLongitudeAttribute()
    // {
    //     return $this->location['lng'] ?? 1;
    // }

}
