<?php

namespace App\Services;

use App\Models\Driver;

class LicenseValidationService
{
    public function checkLicenseStatus(Driver $driver): array
    {
        $daysRemaining = $driver->getLicenseExpiryDays();

        if ($daysRemaining === null) {
            return [
                'valid' => false,
                'blocked' => true,
                'warning' => false,
                'days_remaining' => null,
                'message' => __('api.license_not_set'),
            ];
        }

        $blocked = $daysRemaining <= 10;
        $warning = $daysRemaining <= 30 && $daysRemaining > 10;
        $valid = $daysRemaining > 30;

        return [
            'valid' => $valid,
            'blocked' => $blocked,
            'warning' => $warning,
            'days_remaining' => $daysRemaining,
            'message' => $this->getWarningMessage($driver),
        ];
    }

    public function canPerformAction(Driver $driver): bool
    {
        $status = $this->checkLicenseStatus($driver);
        return !$status['blocked'];
    }

    public function getWarningMessage(Driver $driver): ?string
    {
        $daysRemaining = $driver->getLicenseExpiryDays();

        if ($daysRemaining === null) {
            return __('api.license_not_set_contact');
        }

        if ($daysRemaining <= 10) {
            return __('api.license_expires_blocked', ['days' => $daysRemaining]);
        }

        if ($daysRemaining <= 30) {
            return __('api.license_expires_warning', ['days' => $daysRemaining]);
        }

        return null;
    }
}








