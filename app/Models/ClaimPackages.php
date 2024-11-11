<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimPackages extends Model
{
    use HasFactory;

    protected $table = 'claims_packages';

    protected $fillable = [
        'claim_id',
        'price',
        'quantity',
        'note',
    ];

    public function claim()
    {
        return $this->belongsTo(Claim::class);
    }
}
