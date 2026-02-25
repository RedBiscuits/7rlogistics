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

        if (DriverAttendance::hasActiveShift($driver->id)) {
            return $this->successResponse([
                'allowed' => false,
                'message' => __('api.already_checked_in'),
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

        $today = now()->toDateString();

        $attendance = DriverAttendance::create([
            'driver_id' => $driver->id,
            'attendance_location_id' => $validation['location']->id,
            'check_in_at' => now(),
            'check_in_latitude' => $request->latitude,
            'check_in_longitude' => $request->longitude,
            'date' => $today,
            'shift_number' => DriverAttendance::nextShiftNumber($driver->id, $today),
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

        $attendance->update([
            'check_out_at' => now(),
            'check_out_latitude' => $request->latitude,
            'check_out_longitude' => $request->longitude,
        ]);

        $hours = $attendance->calculateHours();
        $attendance->hours_worked = $hours !== null ? min($hours, 999.99) : null;
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

        $activeShift = DriverAttendance::forDriver($driver->id)
            ->checkedIn()
            ->latest('check_in_at')
            ->first();

        $completedHours = (float) DriverAttendance::forDriver($driver->id)
            ->forDate(now()->toDateString())
            ->completed()
            ->sum('hours_worked');

        $shiftsToday = (int) DriverAttendance::forDriver($driver->id)
            ->forDate(now()->toDateString())
            ->count();

        return response()->json([
            'checked_in' => $activeShift !== null,
            'current_attendance' => $activeShift ? new AttendanceResource($activeShift) : null,
            'hours_today' => $completedHours,
            'shifts_today' => $shiftsToday,
        ]);
    }

    public function getHistory(Request $request): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        $query = DriverAttendance::forDriver($driver->id)
            ->completed()
            ->with('location')
            ->orderBy('date', 'desc')
            ->orderBy('shift_number', 'asc');

        $fromDate = $request->input('from') ?? $request->input('from_date');
        $toDate = $request->input('to') ?? $request->input('to_date');

        if ($fromDate) {
            $query->whereDate('date', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('date', '<=', $toDate);
        }

        $page = $request->input('page', 1);
        $limit = $request->input('limit', 20);
        
        $attendances = $query->paginate($limit, ['*'], 'page', $page);

        return $this->successResponse(AttendanceResource::collection($attendances->items()));
    }

    public function getToday(Request $request): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        $shifts = DriverAttendance::forDriver($driver->id)
            ->forDate(now()->toDateString())
            ->with('location')
            ->orderBy('shift_number', 'asc')
            ->get();

        // Also check for any unchecked-out shift from a previous day (e.g. driver forgot to check out)
        $activeShift = $shifts->whereNull('check_out_at')->first();
        if (!$activeShift) {
            $staleShift = DriverAttendance::forDriver($driver->id)
                ->checkedIn()
                ->with('location')
                ->latest('check_in_at')
                ->first();
            if ($staleShift) {
                $activeShift = $staleShift;
                // Include it in the shifts collection so the UI can display it
                $shifts->push($staleShift);
            }
        }

        $completedHours = (float) $shifts->whereNotNull('check_out_at')->sum('hours_worked');

        // Backward compat: top-level fields from the latest shift
        $latest = $shifts->last();

        $response = [
            'shifts' => AttendanceResource::collection($shifts),
            'totalHoursToday' => round($completedHours, 2),
            'activeShift' => $activeShift ? new AttendanceResource($activeShift) : null,
            'date' => now()->toDateString(),
        ];

        // Backward compat flat fields from latest shift
        if ($latest) {
            $response['id'] = (string) $latest->id;
            $response['driverId'] = (string) $latest->driver_id;
            $response['checkInTime'] = $latest->check_in_at?->format('Y-m-d\TH:i:s\Z');
            $response['checkOutTime'] = $latest->check_out_at?->format('Y-m-d\TH:i:s\Z');
            $response['status'] = $latest->check_out_at ? 'checked_out' : 'checked_in';
            $response['hoursWorked'] = $latest->hours_worked ? (float) $latest->hours_worked : null;
        }

        return $this->successResponse($shifts->isEmpty() ? null : $response);
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
            ->orderBy('date')
            ->orderBy('shift_number')
            ->get();

        $totalShifts = $attendances->count();
        $totalDays = $attendances->pluck('date')->unique()->count();
        $totalHours = $attendances->sum('hours_worked');
        $averageHoursPerDay = $totalDays > 0 ? round($totalHours / $totalDays, 2) : 0;

        return response()->json([
            'year' => $year,
            'month' => $month,
            'total_days_worked' => $totalDays,
            'total_shifts' => $totalShifts,
            'total_hours' => round($totalHours, 2),
            'average_hours_per_day' => $averageHoursPerDay,
            'attendances' => AttendanceResource::collection($attendances),
        ]);
    }

    public function getLocations(Request $request): JsonResponse
    {
        $locations = AttendanceLocation::active()->get();

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
