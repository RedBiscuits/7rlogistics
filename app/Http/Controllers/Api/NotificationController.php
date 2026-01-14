<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\DriverNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        $query = DriverNotification::where('driver_id', $driver->id)
            ->with('shipment')
            ->orderBy('created_at', 'desc');

        // Filter by unread
        if ($request->has('unread')) {
            $unread = filter_var($request->input('unread'), FILTER_VALIDATE_BOOLEAN);
            if ($unread) {
                $query->where('is_read', false);
            }
        }

        // Pagination
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 20);
        $notifications = $query->paginate($limit, ['*'], 'page', $page);

        // Return array directly in data
        return $this->successResponse(NotificationResource::collection($notifications->items()));
    }

    public function markAsRead(DriverNotification $notification, Request $request): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        // Verify notification belongs to driver
        if ($notification->driver_id !== $driver->id) {
            return $this->errorResponse(__('api.unauthorized'), 403);
        }

        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
        $notification->refresh();
        $notification->load('shipment');

        return $this->successResponse(new NotificationResource($notification));
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        DriverNotification::where('driver_id', $driver->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return $this->successResponse(null);
    }

    public function countUnread(Request $request): JsonResponse
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        $count = DriverNotification::where('driver_id', $driver->id)
            ->where('is_read', false)
            ->count();

        return $this->successResponse($count);
    }
}
