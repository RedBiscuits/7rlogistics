<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Driver extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'name',
        'code',
        'id_number',
        'id_expire_date',
        'driving_license_number',
        'driving_license_expire_date',
        'car_type',
        'car_license',
        'car_license_expire_date',
        'location',
        'city',
        'contact_number',
        'contact_number_2',
        'contact_number_3',
    ];

    protected $casts = [
        'id_expire_date' => 'date',
        'driving_license_expire_date' => 'date',
        'car_license_expire_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function missions()
    {
        return $this->hasMany(Shipment::class, 'driver_id');
    }
}
