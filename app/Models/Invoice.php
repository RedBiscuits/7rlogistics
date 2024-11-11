<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    protected static function boot()
    {
        parent::boot();
        // Event handler for creating
        static::creating(function ($model) {
            $model->creator_id = auth()->user()->id;
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
