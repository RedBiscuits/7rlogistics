<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\DriverAttendance;
use App\Enums\ShipmentStatus;
use App\Services\LicenseValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private LicenseValidationService $licenseValidationService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        // Save driver's language preference from Accept-Language header
        $currentLocale = app()->getLocale();
        if ($driver->lang !== $currentLocale) {
            $driver->update(['lang' => $currentLocale]);
        }

        // Current attendance status
        $todayAttendance = DriverAttendance::forDriver($driver->id)
            ->forDate(now()->toDateString())
            ->checkedIn()
            ->first();

        $hoursToday = DriverAttendance::forDriver($driver->id)
            ->forDate(now()->toDateString())
            ->completed()
            ->sum('hours_worked');

        // Shipment counts
        $upcomingShipments = $driver->missions()
            ->where('status', ShipmentStatus::ASSIGNEDMISSION)
            ->count();

        $currentShipments = $driver->missions()
            ->whereIn('status', [
                ShipmentStatus::DRIVER_ACKNOWLEDGED,
                ShipmentStatus::DRIVER_ON_WAY_TO_COLLECT,
                ShipmentStatus::AWAITING_COLLECTION,
                ShipmentStatus::DRIVER_RECEIVED,
                ShipmentStatus::ON_ITS_WAY,
            ])
            ->count();

        $completedShipments = $driver->missions()
            ->where('status', ShipmentStatus::DELIVERED)
            ->count();

        // Unread notifications
        $unreadNotifications = $driver->notifications()
            ->unread()
            ->count();

        // License status
        $licenseStatus = $this->licenseValidationService->checkLicenseStatus($driver);

        $data = [
            'attendance' => [
                'checked_in' => $todayAttendance !== null,
                'current_attendance' => $todayAttendance ? [
                    'id' => $todayAttendance->id,
                    'check_in_at' => $todayAttendance->check_in_at->format('Y-m-d H:i:s'),
                    'location' => $todayAttendance->location ? [
                        'id' => $todayAttendance->location->id,
                        'name' => $todayAttendance->location->name,
                    ] : null,
                ] : null,
                'hours_today' => (float) $hoursToday,
            ],
            'shipments' => [
                'upcoming' => $upcomingShipments,
                'current' => $currentShipments,
                'completed' => $completedShipments,
            ],
            'notifications' => [
                'unread_count' => $unreadNotifications,
            ],
            'license_status' => $licenseStatus,
        ];

        return $this->successResponse($data);
    }
}
