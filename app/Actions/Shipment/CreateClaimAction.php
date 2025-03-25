<?php

namespace App\Actions\Shipment;

use App\Models\Shipment;
use App\Models\Claim;
use App\Models\ClaimPackages;

class CreateClaimAction
{
    public static function execute(Shipment $shipment, string $notes, float $price, int $quantity)
    {
        $claim = Claim::firstOrCreate( [
            'shipment_id' => $shipment->id,
            'creator_id' => auth()->user()->id,
        ], [
            'notes' => $notes,
        ]);

        ClaimPackages::create([
            'claim_id' => $claim->id,
            'price' => $price,
            'quantity' => $quantity,
            'note' => $notes
        ]);

        return $claim;
    }
}