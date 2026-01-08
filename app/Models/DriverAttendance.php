<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class DriverAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'attendance_location_id',
        'check_in_at',
        'check_in_latitude',
        'check_in_longitude',
        'check_out_at',
        'check_out_latitude',
        'check_out_longitude',
        'hours_worked',
        'date',
    ];

    protected $casts = [
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
        'check_in_latitude' => 'decimal:8',
        'check_in_longitude' => 'decimal:8',
        'check_out_latitude' => 'decimal:8',
        'check_out_longitude' => 'decimal:8',
        'hours_worked' => 'decimal:2',
        'date' => 'date',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(AttendanceLocation::class, 'attendance_location_id');
    }

    public function scopeForDriver(Builder $query, int $driverId): Builder
    {
        return $query->where('driver_id', $driverId);
    }

    public function scopeForDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForMonth(Builder $query, int $year, int $month): Builder
    {
        return $query->whereYear('date', $year)
            ->whereMonth('date', $month);
    }

    public function scopeCheckedIn(Builder $query): Builder
    {
        return $query->whereNull('check_out_at');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->whereNotNull('check_out_at');
    }

    /**
     * Calculate hours worked from check-in and check-out times
     */
    public function calculateHours(): ?float
    {
        if (!$this->check_in_at || !$this->check_out_at) {
            return null;
        }

        $hours = $this->check_in_at->diffInMinutes($this->check_out_at) / 60;
        return round($hours, 2);
    }

    /**
     * Auto-calculate hours when check-out is set
     */
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($attendance) {
            if ($attendance->isDirty('check_out_at') && $attendance->check_out_at) {
                $attendance->hours_worked = $attendance->calculateHours();
            }
        });
    }

    /**
     * Get hours worked (calculated if not set)
     */
    public function getHoursWorkedAttribute($value): ?float
    {
        if ($value !== null) {
            return (float) $value;
        }

        return $this->calculateHours();
    }
}
