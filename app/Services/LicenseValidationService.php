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
                'message' => 'License expiry date not set. Please contact management.',
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
            return 'License expiry date not set. Please contact management for data update.';
        }

        if ($daysRemaining <= 10) {
            return "License expires in {$daysRemaining} days. Please contact management for data update.";
        }

        if ($daysRemaining <= 30) {
            return "License expires in {$daysRemaining} days. Please renew your license soon.";
        }

        return null;
    }
}








