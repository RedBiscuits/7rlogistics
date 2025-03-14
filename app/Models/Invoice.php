<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $shipment_id
 * @property float $price
 * @property floar $discount
 * @property int $status
 * @property int $creator_id
 * @property int $payment_type
 * @property string $reference
 * @property string $notes
 * @property int $creator_id
 * @property \Carbon\Carbon $collected_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
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
