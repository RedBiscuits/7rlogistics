<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceLocationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'radius' => $this->radius,
            'is_active' => $this->is_active,
            'description' => $this->description,
            'distance' => $this->distance ?? null, // Set by controller if coordinates provided
            'distance_formatted' => $this->distance ? $this->formatDistance($this->distance) : null,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    private function formatDistance(float $meters): string
    {
        if ($meters < 1000) {
            return round($meters) . 'm';
        }
        
        return round($meters / 1000, 2) . 'km';
    }
}
