<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Map notification type to mobile app string values
        $typeString = match ($this->type) {
            \App\Enums\NotificationType::SHIPMENT_ASSIGNED => 'new_shipment',
            \App\Enums\NotificationType::LICENSE_EXPIRY => 'alert',
            \App\Enums\NotificationType::GENERAL => 'message',
            default => 'message',
        };

        return [
            'id' => (string) $this->id,
            'type' => $typeString,
            'title' => $this->title,
            'message' => $this->body,
            'read' => (bool) $this->is_read,
            'shipmentId' => $this->shipment_id ? (string) $this->shipment_id : null,
            'createdAt' => $this->created_at->format('Y-m-d\TH:i:s\Z'),
        ];
    }
}
