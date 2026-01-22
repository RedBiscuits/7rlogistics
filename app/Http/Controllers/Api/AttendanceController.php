<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CheckInRequest;
use App\Http\Requests\Api\CheckOutRequest;
use App\Http\Resources\AttendanceResource;
use App\Http\Resources\AttendanceLocationResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\AttendanceLocation;
use App\Models\DriverAttendance;
use App\Services\GeofencingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private GeofencingService $geofencingService
    ) {
    }

    public function checkIn(CheckInRequest $request): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        // Check if already checked in today (once per day validation)
        $todayAttendance = DriverAttendance::forDriver($driver->id)
            ->forDate(now()->toDateString())
            ->first();

        if ($todayAttendance) {
            return $this->successResponse([
                'allowed' => false,
                'message' => __('api.already_checked_in_today'),
            ]);
        }

        // Validate location
        $validation = $this->geofencingService->validateLocation(
            $request->latitude,
            $request->longitude,
            $request->location_id
        );

        if (!$validation['valid']) {
            $message = __('api.not_at_allowed_location');
            if (isset($validation['nearest']) && $validation['nearest']) {
                $nearest = $validation['nearest'];
                $distance = round($nearest['distance']);
                $message .= '. ' . __('api.nearest_location', ['name' => $nearest['location_name'], 'distance' => $distance]);
            } elseif (empty(AttendanceLocation::active()->count())) {
                $message .= '. ' . __('api.no_locations_configured');
            }
            
            return $this->successResponse([
                'allowed' => false,
                'message' => $message,
            ]);
        }

        // Create attendance record
        $attendance = DriverAttendance::create([
            'driver_id' => $driver->id,
            'attendance_location_id' => $validation['location']->id,
            'check_in_at' => now(),
            'check_in_latitude' => $request->latitude,
            'check_in_longitude' => $request->longitude,
            'date' => now()->toDateString(),
        ]);

        $attendance->load(['location', 'driver']);

        return $this->successResponse([
            'allowed' => true,
            'message' => __('api.check_in_success'),
            'record' => new AttendanceResource($attendance),
        ]);
    }

    public function checkOut(CheckOutRequest $request): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        // Find active check-in
        $attendance = DriverAttendance::forDriver($driver->id)
            ->checkedIn()
            ->latest('check_in_at')
            ->first();

        if (!$attendance) {
            return $this->successResponse([
                'allowed' => false,
                'message' => __('api.no_active_checkin'),
            ]);
        }

        // Update check-out
        $attendance->update([
            'check_out_at' => now(),
            'check_out_latitude' => $request->latitude,
            'check_out_longitude' => $request->longitude,
        ]);

        // Calculate hours worked
        $attendance->hours_worked = $attendance->calculateHours();
        $attendance->save();
        $attendance->refresh();
        $attendance->load(['location', 'driver']);

        return $this->successResponse([
            'allowed' => true,
            'message' => __('api.check_out_success'),
            'record' => new AttendanceResource($attendance),
        ]);
    }

    public function getStatus(Request $request): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        $todayAttendance = DriverAttendance::forDriver($driver->id)
            ->forDate(now()->toDateString())
            ->checkedIn()
            ->first();

        $hoursToday = DriverAttendance::forDriver($driver->id)
            ->forDate(now()->toDateString())
            ->completed()
            ->sum('hours_worked');

        return response()->json([
            'checked_in' => $todayAttendance !== null,
            'current_attendance' => $todayAttendance ? new AttendanceResource($todayAttendance) : null,
            'hours_today' => (float) $hoursToday,
        ]);
    }

    public function getHistory(Request $request): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        $query = DriverAttendance::forDriver($driver->id)
            ->completed()
            ->with('location')
            ->orderBy('date', 'desc');

        // Date range filter - support both 'from'/'to' and 'from_date'/'to_date'
        $fromDate = $request->input('from') ?? $request->input('from_date');
        $toDate = $request->input('to') ?? $request->input('to_date');

        if ($fromDate) {
            $query->whereDate('date', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('date', '<=', $toDate);
        }

        // Pagination
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 20);
        
        $attendances = $query->paginate($limit, ['*'], 'page', $page);

        // Return array directly in data, not paginated object
        return $this->successResponse(AttendanceResource::collection($attendances->items()));
    }

    public function getToday(Request $request): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        $attendance = DriverAttendance::forDriver($driver->id)
            ->forDate(now()->toDateString())
            ->with('location')
            ->first();

        return $this->successResponse($attendance ? new AttendanceResource($attendance) : null);
    }

    public function getMonthlySummary(Request $request): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $attendances = DriverAttendance::forDriver($driver->id)
            ->forMonth($year, $month)
            ->completed()
            ->get();

        $totalDays = $attendances->count();
        $totalHours = $attendances->sum('hours_worked');
        $averageHours = $totalDays > 0 ? round($totalHours / $totalDays, 2) : 0;

        return response()->json([
            'year' => $year,
            'month' => $month,
            'total_days_worked' => $totalDays,
            'total_hours' => round($totalHours, 2),
            'average_hours_per_day' => $averageHours,
            'attendances' => AttendanceResource::collection($attendances),
        ]);
    }

    public function getLocations(Request $request): JsonResponse
    {
        $locations = \App\Models\AttendanceLocation::active()->get();

        // If coordinates provided, calculate distance
        $lat = $request->input('latitude') ?? $request->input('lat');
        $lng = $request->input('longitude') ?? $request->input('lng');
        
        if ($lat && $lng) {
            foreach ($locations as $location) {
                $location->distance = $location->distanceTo($lat, $lng);
            }
            
            $locations = $locations->sortBy('distance')->values();
        }

        return $this->successResponse(AttendanceLocationResource::collection($locations));
    }
}
