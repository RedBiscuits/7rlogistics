<?php

namespace App\Models\Traits;

use App\Enums\ModelChangeType;
use App\Models\ModelChangeNotification;
use App\Services\ModelChangeMessageFormatter;
use Illuminate\Database\Eloquent\Model;

trait RecordsModelChangeNotifications
{
    protected static array $originalValuesForUpdate = [];

    public static function bootRecordsModelChangeNotifications(): void
    {
        static::creating(function (Model $model): void {
            // No-op; we only record after the model is created so we have an ID
        });

        static::created(function (Model $model): void {
            self::recordIfFromTrackedSource($model, ModelChangeType::CREATED, [], []);
        });

        static::updating(function (Model $model): void {
            $dirty = $model->getDirty();
            if (count($dirty) === 0) {
                return;
            }
            $raw = $model->getRawOriginal();
            self::$originalValuesForUpdate[spl_object_id($model)] = array_intersect_key(
                $raw,
                array_flip(array_keys($dirty))
            );
        });

        static::updated(function (Model $model): void {
            $oid = spl_object_id($model);
            $original = self::$originalValuesForUpdate[$oid] ?? [];
            unset(self::$originalValuesForUpdate[$oid]);
            $changes = $model->getChanges();
            $source = app()->bound('model_change.source') ? app('model_change.source') : null;
            $fromOperations = $source === \App\Enums\ModelChangeSource::OPERATIONS_PANEL;
            if (! $fromOperations && ! self::shouldRecordUpdate($model, $changes)) {
                return;
            }
            if ($fromOperations) {
                $meaningful = array_diff(array_keys($changes), ['updated_at', 'created_at']);
                if (count($meaningful) === 0) {
                    return;
                }
            }
            self::recordIfFromTrackedSource($model, ModelChangeType::UPDATED, $changes, $original);
        });

        static::deleted(function (Model $model): void {
            self::recordIfFromTrackedSource($model, ModelChangeType::DELETED, [], []);
        });
    }

    protected static function shouldRecordUpdate(Model $model, array $changes): bool
    {
        $meaningfulKeys = array_diff(
            array_keys($changes),
            ['updated_at', 'created_at']
        );
        if (count($meaningfulKeys) === 0) {
            return false;
        }

        if ($model instanceof \App\Models\Shipment) {
            return array_key_exists('status', $changes);
        }

        if ($model instanceof \App\Models\DriverAttendance) {
            return array_key_exists('check_out_at', $changes);
        }

        if ($model instanceof \App\Models\Driver) {
            return count($meaningfulKeys) > 0;
        }

        return true;
    }

    protected static function recordIfFromTrackedSource(
        Model $model,
        ModelChangeType $changeType,
        array $changes,
        array $original
    ): void {
        $source = app()->bound('model_change.source') ? app('model_change.source') : null;
        if ($source === null) {
            return;
        }

        $actor = app()->bound('model_change.actor') ? app('model_change.actor') : null;

        $formatter = app(ModelChangeMessageFormatter::class);
        $message = $formatter->format($model, $changeType, $actor, $changes, $original);

        ModelChangeNotification::create([
            'subject_type' => $model->getMorphClass(),
            'subject_id' => $model->getKey(),
            'change_type' => $changeType,
            'source' => $source,
            'actor_type' => $actor ? $actor->getMorphClass() : null,
            'actor_id' => $actor?->getKey(),
            'message' => $message,
        ]);
    }
}
