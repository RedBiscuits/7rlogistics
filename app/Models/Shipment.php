<?php

namespace App\Models;

use App\Enums\ShipmentStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\Traits\RecordsModelChangeNotifications;

/**
 * @property ShipmentStatus $status
 * @property string $id
 * @property string $pickup_date
 * @property string $delivery_date
 * @property string $description
 * @property string $notes
 * @property string $instructions
 * @property string $reference
 * @property string $tracking_number
 * @property string $driver_id
 * @property string $truck_id
 * @property City $fromCity
 * @property Area $fromArea
 * @property City $toCity
 * @property Area $toArea
 * @property Invoice $invoice
 * @property Claim $claim
 * @property User $creator
 * @property User $driver
 * @property User $user
 * @property Truck $truck
 * @property Destination $destination
 * @property Area $fromArea
 * @property Area $toArea
 * @property City $fromCity
 * @property City $toCity
 */
class Shipment extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, RecordsModelChangeNotifications;

    protected $guarded = [];

    protected $casts = [
        'status' => ShipmentStatus::class,
        'pickup_date' => 'date:Y-m-d H:i',
        'driver_acknowledged_at' => 'datetime',
        'driver_on_way_to_collect_at' => 'datetime',
        'awaiting_collection_at' => 'datetime',
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

    public function company()
    {
        return $this->belongsTo(Company::class);
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

    public function notifications()
    {
        return $this->hasMany(DriverNotification::class);
    }

    public function stops()
    {
        return $this->hasMany(ShipmentStop::class)->orderBy('type')->orderBy('sequence');
    }

    public function pickupStops()
    {
        return $this->hasMany(ShipmentStop::class)->where('type', 'pickup')->orderBy('sequence');
    }

    public function deliveryStops()
    {
        return $this->hasMany(ShipmentStop::class)->where('type', 'delivery')->orderBy('sequence');
    }

    /**
     * Scope: only shipments visible to drivers (exclude Pending and Cancelled).
     * Drivers see shipments only after admin approval (APPROVED or later).
     */
    public function scopeVisibleToDriver($query)
    {
        return $query->whereNotIn('status', [
            \App\Enums\ShipmentStatus::PENDING,
            \App\Enums\ShipmentStatus::CANCELLED,
        ]);
    }

    public function getDeliveryProofs()
    {
        return $this->getMedia('delivery_proofs');
    }

    public function hasMinimumProofs(): bool
    {
        $proofs = $this->getMedia('delivery_proofs');
        return $proofs->count() >= 2;
    }

    /**
     * Get pickup photos count
     */
    public function getPickupPhotosCount(): int
    {
        return $this->getMedia('pickup_photos')->count();
    }

    /**
     * Get delivery photos count
     */
    public function getDeliveryPhotosCount(): int
    {
        return $this->getMedia('delivery_photos')->count();
    }

    /**
     * Check if shipment has minimum pickup photos (2 required)
     */
    public function hasMinimumPickupPhotos(): bool
    {
        return $this->getPickupPhotosCount() >= 2;
    }

    /**
     * Check if shipment has minimum delivery photos (2 required)
     */
    public function hasMinimumDeliveryPhotos(): bool
    {
        return $this->getDeliveryPhotosCount() >= 2;
    }

    /**
     * Check if shipment has a signature
     */
    public function hasSignature(): bool
    {
        return $this->getMedia('signature')->count() > 0;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('delivery_proofs')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg']);

        $this->addMediaCollection('receipt_proofs')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg']);

        $this->addMediaCollection('pod_proofs')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg']);

        // New collections for mobile app
        $this->addMediaCollection('pickup_photos')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg']);

        $this->addMediaCollection('delivery_photos')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg']);

        $this->addMediaCollection('toll_photos')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg']);

        $this->addMediaCollection('signature')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->performOnCollections('delivery_proofs', 'receipt_proofs', 'pod_proofs', 'pickup_photos', 'delivery_photos', 'toll_photos', 'signature');
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
