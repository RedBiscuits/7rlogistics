<?php

namespace App\Policies;

use App\Models\DriverAttendance;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DriverAttendancePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any attendance records (admin panel).
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DriverAttendance $driverAttendance): bool
    {
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false; // Attendances are created via driver API
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DriverAttendance $driverAttendance): bool
    {
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DriverAttendance $driverAttendance): bool
    {
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasRole('Super Admin');
    }
}
