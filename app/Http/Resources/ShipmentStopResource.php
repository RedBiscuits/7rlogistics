<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentStopResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'sequence' => $this->sequence,
            'name' => $this->name,
            'location' => $this->location,
            'contactName' => $this->contact_name,
            'contactPhone' => $this->contact_phone,
            'notes' => $this->notes,
        ];
    }
}
