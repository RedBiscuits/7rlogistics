<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'creator_id',
        'discount',
        'status',
        'payment_type',
        'collected_at',
        'reference',
        'notes',
    ];

    protected static function boot()
    {
        parent::boot();
        // Event handler for creating
        static::creating(function ($model) {
            $model->creator_id = auth()->user()->id;
        });
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function packages()
    {
        return $this->hasMany(ClaimPackages::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
