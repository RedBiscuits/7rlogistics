<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Models\Traits\RecordsModelChangeNotifications;

class Company extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, RecordsModelChangeNotifications;

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
        return $this->hasMany(Shipment::class);
    }

    // public function registerMediaCollections(): void
    // {
    //     $this->addMediaCollection('attachments')
    //         ->useDisk('public');
    // }
}
