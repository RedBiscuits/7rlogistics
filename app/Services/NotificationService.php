<?php

namespace App\Services;

use App\Enums\DeviceType;
use App\Enums\NotificationType;
use App\Models\Driver;
use App\Models\DriverDevice;
use App\Models\DriverNotification;
use App\Models\Shipment;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Exception\Messaging\InvalidArgument;
use Kreait\Firebase\Exception\Messaging\NotFound;
use Kreait\Laravel\Firebase\Facades\Firebase;

class NotificationService
{
    public function notifyShipmentAssigned(Driver $driver, Shipment $shipment): void
    {
        $locale = $driver->lang ?? 'ar';
        
        $title = __('api.notification_shipment_assigned_title', [], $locale);
        $body = __('api.notification_shipment_assigned_body', ['id' => $shipment->id], $locale);
        $bodyDetailed = __('api.notification_shipment_assigned_body_detailed', [
            'id' => $shipment->id,
            'from' => $shipment->fromArea->name ?? '',
            'to' => $shipment->toArea->name ?? '',
        ], $locale);

        // Create database notification
        DriverNotification::create([
            'driver_id' => $driver->id,
            'shipment_id' => $shipment->id,
            'title' => $title,
            'body' => $body,
            'type' => NotificationType::SHIPMENT_ASSIGNED,
        ]);

        // Send push notification
        $this->sendPushNotification($driver, [
            'title' => $title,
            'body' => $bodyDetailed,
            'data' => [
                'type' => 'shipment_assigned',
                'shipment_id' => $shipment->id,
            ],
        ]);
    }

    public function notifyLicenseExpiry(Driver $driver): void
    {
        $daysRemaining = $driver->getLicenseExpiryDays();
        
        if ($daysRemaining && $daysRemaining <= 30) {
            $locale = $driver->lang ?? 'ar';
            
            $title = __('api.notification_license_expiry_title', [], $locale);
            $body = __('api.notification_license_expiry_body', ['days' => $daysRemaining], $locale);
            $bodyDetailed = __('api.notification_license_expiry_body_detailed', ['days' => $daysRemaining], $locale);

            DriverNotification::create([
                'driver_id' => $driver->id,
                'title' => $title,
                'body' => $body,
                'type' => NotificationType::LICENSE_EXPIRY,
            ]);

            $this->sendPushNotification($driver, [
                'title' => $title,
                'body' => $bodyDetailed,
                'data' => [
                    'type' => 'license_expiry',
                    'days_remaining' => $daysRemaining,
                ],
            ]);
        }
    }

    public function notifyPickupReminder(Driver $driver, Shipment $shipment): void
    {
        // Check if reminder already sent for this shipment
        $existingNotification = DriverNotification::where('driver_id', $driver->id)
            ->where('shipment_id', $shipment->id)
            ->where('type', NotificationType::PICKUP_REMINDER)
            ->exists();

        if ($existingNotification) {
            return; // Already notified
        }

        $locale = $driver->lang ?? 'ar';
        $areaName = $shipment->fromArea ? $shipment->fromArea->name : 'Unknown Area';
        $pickupDate = $shipment->pickup_date->format('Y-m-d H:i');

        $title = __('api.notification_pickup_reminder_title', [], $locale);
        $body = __('api.notification_pickup_reminder_body', ['awb' => $shipment->awb, 'area' => $areaName], $locale);
        $bodyDetailed = __('api.notification_pickup_reminder_body_detailed', [
            'awb' => $shipment->awb,
            'date' => $pickupDate,
            'area' => $areaName,
        ], $locale);

        // Create database notification
        DriverNotification::create([
            'driver_id' => $driver->id,
            'shipment_id' => $shipment->id,
            'title' => $title,
            'body' => $body,
            'type' => NotificationType::PICKUP_REMINDER,
        ]);

        // Send push notification
        $this->sendPushNotification($driver, [
            'title' => $title,
            'body' => $bodyDetailed,
            'data' => [
                'type' => 'pickup_reminder',
                'shipment_id' => $shipment->id,
                'awb' => $shipment->awb,
                'area' => $areaName,
                'pickup_date' => $pickupDate,
            ],
        ]);
    }

    public function notifyPendingAcceptanceReminder(Driver $driver, Shipment $shipment): void
    {
        $locale = $driver->lang ?? 'ar';

        $title = __('api.notification_pending_acceptance_title', [], $locale);
        $body = __('api.notification_pending_acceptance_body', [
            'id' => $shipment->id,
        ], $locale);

        // Store as a general notification to keep history
        DriverNotification::create([
            'driver_id' => $driver->id,
            'shipment_id' => $shipment->id,
            'title' => $title,
            'body' => $body,
            'type' => NotificationType::GENERAL,
        ]);

        $this->sendPushNotification($driver, [
            'title' => $title,
            'body' => $body,
            'data' => [
                'type' => 'shipment_pending_acceptance',
                'shipment_id' => $shipment->id,
            ],
        ]);
    }

    public function sendPushNotification(Driver $driver, array $payload): void
    {
        $devices = $driver->devices()->active()->get();
        
        if ($devices->isEmpty()) {
            \Log::info('No active devices found for driver', [
                'driver_id' => $driver->id,
            ]);
            return;
        }

        try {
            $messaging = Firebase::messaging();
        } catch (\Exception $e) {
            \Log::error('Failed to initialize Firebase Messaging', [
                'error' => $e->getMessage(),
                'driver_id' => $driver->id,
            ]);
            return;
        }

        foreach ($devices as $device) {
            try {
                // Build FCM message
                $message = \Kreait\Firebase\Messaging\CloudMessage::withTarget('token', $device->device_token)
                    ->withNotification(
                        \Kreait\Firebase\Messaging\Notification::create(
                            $payload['title'] ?? 'Notification',
                            $payload['body'] ?? ''
                        )
                    )
                    ->withData($payload['data'] ?? []);

                // Set Android-specific options if device is Android
                if ($device->device_type === DeviceType::ANDROID) {
                    $message = $message->withAndroidConfig(
                        \Kreait\Firebase\Messaging\AndroidConfig::fromArray([
                            'priority' => 'high',
                            'notification' => [
                                'sound' => 'default',
                                'channel_id' => 'default',
                            ],
                        ])
                    );
                }

                // Set iOS-specific options if device is iOS
                if ($device->device_type === DeviceType::IOS) {
                    $message = $message->withApnsConfig(
                        \Kreait\Firebase\Messaging\ApnsConfig::fromArray([
                            'headers' => [
                                'apns-priority' => '10',
                            ],
                            'payload' => [
                                'aps' => [
                                    'sound' => 'default',
                                    'badge' => 1,
                                ],
                            ],
                        ])
                    );
                }

                // Send the message
                $messaging->send($message);

                \Log::info('Push notification sent successfully', [
                    'driver_id' => $driver->id,
                    'device_id' => $device->id,
                    'device_token' => substr($device->device_token, 0, 20) . '...',
                ]);
            } catch (InvalidArgument $e) {
                // Invalid token - mark device as inactive
                \Log::warning('Invalid device token, marking device as inactive', [
                    'driver_id' => $driver->id,
                    'device_id' => $device->id,
                    'error' => $e->getMessage(),
                ]);
                $device->markAsInactive();
            } catch (NotFound $e) {
                // Token not found - mark device as inactive
                \Log::warning('Device token not found, marking device as inactive', [
                    'driver_id' => $driver->id,
                    'device_id' => $device->id,
                    'error' => $e->getMessage(),
                ]);
                $device->markAsInactive();
            } catch (\Exception $e) {
                \Log::error('Failed to send push notification', [
                    'driver_id' => $driver->id,
                    'device_id' => $device->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    }

    public function registerDevice(Driver $driver, string $token, int $deviceType): DriverDevice
    {
        $payload = [
            'driver_id' => $driver->id,
            'device_type' => $deviceType,
            'is_active' => true,
            'last_used_at' => now(),
        ];

        return DB::transaction(function () use ($driver, $token, $deviceType, $payload) {
            // Lock existing row to avoid duplicate key under concurrent requests
            $device = DriverDevice::where('device_token', $token)->lockForUpdate()->first();

            if ($device) {
                $device->update($payload);
                return $device;
            }

            try {
                return DriverDevice::create([
                    'driver_id' => $driver->id,
                    'device_token' => $token,
                    'device_type' => $deviceType,
                    'is_active' => true,
                    'last_used_at' => now(),
                ]);
            } catch (\PDOException|QueryException $e) {
                // Duplicate entry (race: another request created the same token)
                $isDuplicate = str_contains($e->getMessage(), 'Duplicate entry')
                    || (string) $e->getCode() === '23000'
                    || (int) $e->getCode() === 1062;
                if ($isDuplicate) {
                    $device = DriverDevice::where('device_token', $token)->first();
                    if ($device) {
                        $device->update($payload);
                        return $device;
                    }
                }
                throw $e;
            }
        });
    }
}







