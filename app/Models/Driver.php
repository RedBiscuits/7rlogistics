<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\Traits\RecordsModelChangeNotifications;

class Driver extends Model implements HasMedia
{
    use HasApiTokens, InteractsWithMedia, RecordsModelChangeNotifications;

    protected $fillable = [
        'user_id',
        'name',
        'code',
        'id_number',
        'id_expire_date',
        'driving_license_number',
        'driving_license_expire_date',
        'license_type',
        'car_type',
        'car_license',
        'car_license_expire_date',
        'location',
        'city',
        'contact_number',
        'contact_number_2',
        'contact_number_3',
        'biometric_token',
        'password',
        'lang',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'id_expire_date' => 'date',
        'driving_license_expire_date' => 'date',
        'car_license_expire_date' => 'date',
        'password' => 'hashed',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function missions()
    {
        return $this->hasMany(Shipment::class, 'driver_id');
    }

    public function notifications()
    {
        return $this->hasMany(DriverNotification::class);
    }

    public function devices()
    {
        return $this->hasMany(DriverDevice::class);
    }

    public function attendances()
    {
        return $this->hasMany(DriverAttendance::class);
    }

    /**
     * Encrypt biometric token before saving
     */
    protected function setBiometricTokenAttribute($value): void
    {
        if ($value) {
            $this->attributes['biometric_token'] = encrypt($value);
        } else {
            $this->attributes['biometric_token'] = null;
        }
    }

    /**
     * Decrypt biometric token when retrieving
     */
    protected function getBiometricTokenAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }
        
        try {
            return decrypt($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Check if biometric authentication is enabled
     */
    public function hasBiometricEnabled(): bool
    {
        return !empty($this->attributes['biometric_token'] ?? null);
    }

    /**
     * Check if password is set
     */
    public function hasPassword(): bool
    {
        return !empty($this->attributes['password'] ?? null);
    }

    /**
     * Validate provided biometric token against stored encrypted token
     */
    public function validateBiometricToken(string $token): bool
    {
        if (!$this->hasBiometricEnabled()) {
            return false;
        }
        
        try {
            $storedToken = decrypt($this->attributes['biometric_token']);
            return hash_equals($storedToken, $token);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getLicenseExpiryDays(): ?int
    {
        if (!$this->driving_license_expire_date) {
            return null;
        }

        return now()->diffInDays($this->driving_license_expire_date, false);
    }

    public function isLicenseBlocked(): bool
    {
        $daysRemaining = $this->getLicenseExpiryDays();
        return $daysRemaining !== null && $daysRemaining <= 10;
    }

    public function isLicenseWarning(): bool
    {
        $daysRemaining = $this->getLicenseExpiryDays();
        return $daysRemaining !== null && $daysRemaining <= 30 && $daysRemaining > 10;
    }

    public function getLicensePhotos()
    {
        return [
            'front' => $this->getFirstMedia('license_front'),
            'back' => $this->getFirstMedia('license_back'),
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('license_front')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg']);

        $this->addMediaCollection('license_back')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg']);

        $this->addMediaCollection('id_photo')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg']);

        $this->addMediaCollection('profile_photo')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->performOnCollections('license_front', 'license_back', 'id_photo', 'profile_photo');
    }
}
