<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;
class Company extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function destinations()
    {
        return $this->hasMany(Destination::class);
    }

    public function shipments()
    {
        return $this->hasManyThrough(
            Shipment::class,
            User::class,
            'company_id', // Foreign key on users table
            'user_id',    // Foreign key on shipments table
            'id',         // Local key on companies table
            'id'         // Local key on users table
        );
    }
}
