<?php

namespace App\Models;

use App\Enums\DeviceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class DriverDevice extends Model
{
    protected $fillable = [
        'driver_id',
        'device_token',
        'device_type',
        'app_version',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'device_type' => DeviceType::class,
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForDriver(Builder $query, int $driverId): Builder
    {
        return $query->where('driver_id', $driverId);
    }

    public function markAsActive(): void
    {
        $this->update([
            'is_active' => true,
            'last_used_at' => now(),
        ]);
    }

    public function markAsInactive(): void
    {
        $this->update([
            'is_active' => false,
        ]);
    }
}
