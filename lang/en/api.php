<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Driver API Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in the driver mobile app API responses.
    |
    */

    // Authentication
    'invalid_credentials' => 'Invalid credentials',
    'invalid_biometric_token' => 'Invalid biometric token',
    'device_registered' => 'Device registered successfully',
    'invalid_user_id' => 'Invalid user ID',
    'invalid_password' => 'Invalid password',
    'biometric_registered' => 'Biometric token registered successfully',
    'biometric_disabled' => 'Biometric authentication disabled successfully',

    // Attendance
    'already_checked_in' => 'You are already checked in today',
    'already_checked_in_today' => 'You have already checked in today. Only one check-in per day is allowed.',
    'not_at_allowed_location' => 'You are not at an allowed location',
    'nearest_location' => 'Nearest location: :name (:distance m away)',
    'no_locations_configured' => 'Please attend at your allowed location',
    'check_in_success' => 'Check in successful',
    'no_active_checkin' => 'No active check-in found',
    'check_out_success' => 'Check out successful',

    // Shipments
    'unauthorized' => 'Unauthorized',
    'failed_upload_photo' => 'Failed to upload photo: :error',
    'failed_upload_signature' => 'Failed to upload signature: :error',
    'proofs_uploaded' => 'Proofs uploaded successfully',
    'failed_upload_proofs' => 'Failed to upload proofs: :error',

    // License
    'license_not_set' => 'License expiry date not set. Please contact management.',
    'license_not_set_contact' => 'License expiry date not set. Please contact management for data update.',
    'license_expires_blocked' => 'License expires in :days days. Please contact management for data update.',
    'license_expires_warning' => 'License expires in :days days. Please renew your license soon.',

    // Notifications
    'notification_shipment_assigned_title' => 'New Shipment Assigned',
    'notification_shipment_assigned_body' => 'Shipment #:id has been assigned to you',
    'notification_shipment_assigned_body_detailed' => 'Shipment #:id - :from to :to',
    'notification_license_expiry_title' => 'License Expiring Soon',
    'notification_license_expiry_body' => 'Your license expires in :days days',
    'notification_license_expiry_body_detailed' => 'Your license expires in :days days. Please contact management.',
    'notification_pickup_reminder_title' => 'Pickup Reminder',
    'notification_pickup_reminder_body' => 'AWB: :awb - Pickup tomorrow from :area',
    'notification_pickup_reminder_body_detailed' => 'AWB: :awb - Pickup tomorrow at :date from :area',
];
