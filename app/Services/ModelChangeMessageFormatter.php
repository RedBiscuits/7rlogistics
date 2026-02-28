<?php

namespace App\Services;

use App\Enums\ModelChangeType;
use App\Enums\ShipmentStatus;
use App\Models\DriverAttendance;
use App\Models\Shipment;
use Illuminate\Database\Eloquent\Model;

class ModelChangeMessageFormatter
{
    /**
     * Build a human-readable message for a model change notification.
     *
     * @param  array<string, mixed>  $changes  Changed attributes (new values) for updated events
     * @param  array<string, mixed>  $original  Original values for changed attributes (for "from X to Y")
     */
    public function format(
        Model $model,
        ModelChangeType $changeType,
        ?object $actor = null,
        array $changes = [],
        array $original = []
    ): string {
        $actorName = $this->actorName($actor);

        if ($model instanceof Shipment) {
            return $this->formatShipment($model, $changeType, $actorName, $changes, $original);
        }

        if ($model instanceof DriverAttendance) {
            return $this->formatDriverAttendance($model, $changeType, $actorName);
        }

        if ($model instanceof \App\Models\Driver) {
            return $this->formatDriver($model, $changeType, $actorName, $changes, $original);
        }

        return $this->fallback($model, $changeType, $actorName);
    }

    private function actorName(?object $actor): string
    {
        if ($actor === null) {
            return 'System';
        }
        if (method_exists($actor, 'getNameAttribute') || isset($actor->name)) {
            return (string) $actor->name;
        }

        return class_basename($actor);
    }

    private function formatShipment(
        Shipment $model,
        ModelChangeType $changeType,
        string $actorName,
        array $changes,
        array $original
    ): string {
        if ($changeType === ModelChangeType::UPDATED && array_key_exists('status', $changes)) {
            $oldRaw = $original['status'] ?? null;
            $newRaw = $changes['status'] ?? $model->status;
            $oldStatus = $oldRaw instanceof ShipmentStatus ? $oldRaw : ShipmentStatus::tryFrom((int) $oldRaw);
            $newStatus = $newRaw instanceof ShipmentStatus ? $newRaw : ShipmentStatus::tryFrom((int) $newRaw);
            $oldLabel = $oldStatus?->getLabel() ?? (string) $oldRaw;
            $newLabel = $newStatus?->getLabel() ?? (string) $newRaw;

            return sprintf(
                "Shipment's status with id %s has been updated from %s to %s by %s",
                $model->getKey(),
                $oldLabel ?: 'N/A',
                $newLabel ?: 'N/A',
                $actorName
            );
        }

        return $this->fallback($model, $changeType, $actorName);
    }

    private function formatDriverAttendance(
        DriverAttendance $model,
        ModelChangeType $changeType,
        string $actorName
    ): string {
        $model->loadMissing('driver');
        $driverName = $model->driver ? $model->driver->name : 'Driver';

        if ($changeType === ModelChangeType::CREATED) {
            $time = $model->check_in_at?->format('H:i') ?? 'N/A';
            $lat = $model->check_in_latitude !== null ? (string) $model->check_in_latitude : 'N/A';
            $lng = $model->check_in_longitude !== null ? (string) $model->check_in_longitude : 'N/A';

            return sprintf(
                '%s checked in at %s at lat %s and lng %s',
                $driverName,
                $time,
                $lat,
                $lng
            );
        }

        if ($changeType === ModelChangeType::UPDATED && $model->check_out_at !== null) {
            $time = $model->check_out_at->format('H:i');
            $lat = $model->check_out_latitude !== null ? (string) $model->check_out_latitude : 'N/A';
            $lng = $model->check_out_longitude !== null ? (string) $model->check_out_longitude : 'N/A';

            return sprintf(
                '%s checked out at %s at lat %s and lng %s',
                $driverName,
                $time,
                $lat,
                $lng
            );
        }

        return $this->fallback($model, $changeType, $actorName);
    }

    private function formatDriver(
        \App\Models\Driver $model,
        ModelChangeType $changeType,
        string $actorName,
        array $changes,
        array $original
    ): string {
        $changedKeys = array_keys($changes);
        $meaningful = array_diff($changedKeys, ['updated_at', 'created_at']);
        if ($changeType === ModelChangeType::UPDATED && count($meaningful) > 0) {
            return sprintf(
                'Driver %s profile was updated by %s',
                $model->name ?? $model->getKey(),
                $actorName
            );
        }

        return $this->fallback($model, $changeType, $actorName);
    }

    private function fallback(Model $model, ModelChangeType $changeType, string $actorName): string
    {
        $type = $changeType->value;
        $label = class_basename($model);
        $id = $model->getKey();

        if ($changeType === ModelChangeType::DELETED) {
            return sprintf('%s #%s was deleted by %s', $label, $id, $actorName);
        }

        return sprintf('%s #%s was %s by %s', $label, $id, $type, $actorName);
    }
}
