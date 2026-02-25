<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class ShipmentStop extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'type',
        'sequence',
        'name',
        'location',
        'contact_name',
        'contact_phone',
        'notes',
    ];

    protected $casts = [
        'sequence' => 'integer',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function scopePickups(Builder $query): Builder
    {
        return $query->where('type', 'pickup');
    }

    public function scopeDeliveries(Builder $query): Builder
    {
        return $query->where('type', 'delivery');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('type')->orderBy('sequence');
    }

    /**
     * Check if the location is a Google Maps URL.
     */
    public function isGoogleMapsLink(): bool
    {
        return $this->location && str_contains($this->location, 'google.com/maps');
    }
}
