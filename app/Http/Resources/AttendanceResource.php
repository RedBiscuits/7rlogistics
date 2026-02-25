<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'driverId' => (string) $this->driver_id,
            'date' => $this->date->format('Y-m-d'),
            'shiftNumber' => (int) ($this->shift_number ?? 1),
            'checkInTime' => $this->check_in_at?->format('Y-m-d\TH:i:s\Z'),
            'checkInLat' => (float) $this->check_in_latitude,
            'checkInLng' => (float) $this->check_in_longitude,
            'checkOutTime' => $this->check_out_at?->format('Y-m-d\TH:i:s\Z'),
            'checkOutLat' => $this->check_out_latitude ? (float) $this->check_out_latitude : null,
            'checkOutLng' => $this->check_out_longitude ? (float) $this->check_out_longitude : null,
            'status' => $this->check_out_at ? 'checked_out' : 'checked_in',
            'hoursWorked' => $this->hours_worked ? (float) $this->hours_worked : null,
        ];
    }
}
