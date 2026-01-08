<?php

namespace App\Services;

use App\Models\AttendanceLocation;

class GeofencingService
{
    /**
     * Calculate distance between two GPS coordinates using Haversine formula
     * Returns distance in meters
     */
    public function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // Earth's radius in meters

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lng1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lng2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Validate if coordinates are within any allowed location
     * Returns validation result with location info
     */
    public function validateLocation(float $lat, float $lng, ?int $locationId = null): array
    {
        $locations = AttendanceLocation::active()->get();
        
        if ($locationId) {
            $targetLocation = $locations->firstWhere('id', $locationId);
            if ($targetLocation && $targetLocation->isWithinRadius($lat, $lng)) {
                return [
                    'valid' => true,
                    'location' => $targetLocation,
                    'distance' => $targetLocation->distanceTo($lat, $lng),
                    'nearest' => null,
                ];
            }
        }

        // Check all active locations
        foreach ($locations as $location) {
            if ($location->isWithinRadius($lat, $lng)) {
                return [
                    'valid' => true,
                    'location' => $location,
                    'distance' => $location->distanceTo($lat, $lng),
                    'nearest' => null,
                ];
            }
        }

        // If not within any location, find nearest
        $nearest = $this->findNearestLocation($lat, $lng);

        return [
            'valid' => false,
            'location' => null,
            'distance' => null,
            'nearest' => $nearest,
        ];
    }

    /**
     * Find nearest attendance location and distance
     */
    public function findNearestLocation(float $lat, float $lng): ?array
    {
        $locations = AttendanceLocation::active()->get();
        
        if ($locations->isEmpty()) {
            return null;
        }

        $nearest = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($locations as $location) {
            $distance = $location->distanceTo($lat, $lng);
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearest = [
                    'location' => $location,
                    'distance' => round($distance, 2),
                ];
            }
        }

        return $nearest ? [
            'location_id' => $nearest['location']->id,
            'location_name' => $nearest['location']->name,
            'distance' => $nearest['distance'],
            'distance_formatted' => $this->formatDistance($nearest['distance']),
        ] : null;
    }

    /**
     * Check if coordinates are within a specific location's radius
     */
    public function isWithinRadius(AttendanceLocation $location, float $lat, float $lng): bool
    {
        return $location->isWithinRadius($lat, $lng);
    }

    /**
     * Format distance in meters to human-readable format
     */
    private function formatDistance(float $meters): string
    {
        if ($meters < 1000) {
            return round($meters) . 'm';
        }
        
        return round($meters / 1000, 2) . 'km';
    }
}







