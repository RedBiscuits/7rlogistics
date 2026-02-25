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
    'invalid_credentials' => 'بيانات الدخول غير صحيحة',
    'invalid_biometric_token' => 'رمز البصمة غير صالح',
    'device_registered' => 'تم تسجيل الجهاز بنجاح',
    'invalid_user_id' => 'معرف المستخدم غير صالح',
    'invalid_password' => 'كلمة المرور غير صحيحة',
    'biometric_registered' => 'تم تسجيل البصمة بنجاح',
    'biometric_disabled' => 'تم تعطيل المصادقة بالبصمة بنجاح',

    // Attendance
    'already_checked_in' => 'لديك وردية نشطة. يرجى تسجيل الانصراف قبل بدء وردية جديدة.',
    'already_checked_in_today' => 'لديك وردية نشطة. يرجى تسجيل الانصراف قبل بدء وردية جديدة.',
    'not_at_allowed_location' => 'أنت لست في موقع مسموح به',
    'nearest_location' => 'أقرب موقع: :name (:distance متر)',
    'no_locations_configured' => 'يرجى الحضور في موقع مسموح به',
    'check_in_success' => 'تم تسجيل الحضور بنجاح',
    'no_active_checkin' => 'لا يوجد تسجيل حضور نشط',
    'check_out_success' => 'تم تسجيل الانصراف بنجاح',

    // Shipments
    'unauthorized' => 'غير مصرح',
    'failed_upload_photo' => 'فشل رفع الصورة: :error',
    'failed_upload_signature' => 'فشل رفع التوقيع: :error',
    'proofs_uploaded' => 'تم رفع الإثباتات بنجاح',
    'failed_upload_proofs' => 'فشل رفع الإثباتات: :error',

    // License
    'license_not_set' => 'لم يتم تحديد تاريخ انتهاء الرخصة. يرجى التواصل مع الإدارة.',
    'license_not_set_contact' => 'لم يتم تحديد تاريخ انتهاء الرخصة. يرجى التواصل مع الإدارة لتحديث البيانات.',
    'license_expires_blocked' => 'تنتهي صلاحية الرخصة خلال :days يوم. يرجى التواصل مع الإدارة لتحديث البيانات.',
    'license_expires_warning' => 'تنتهي صلاحية الرخصة خلال :days يوم. يرجى تجديد رخصتك قريباً.',

    // Notifications
    'notification_shipment_assigned_title' => 'شحنة جديدة',
    'notification_shipment_assigned_body' => 'تم تعيين الشحنة #:id لك',
    'notification_shipment_assigned_body_detailed' => 'الشحنة #:id - من :from إلى :to',
    'notification_license_expiry_title' => 'الرخصة تنتهي قريباً',
    'notification_license_expiry_body' => 'تنتهي رخصتك خلال :days يوم',
    'notification_license_expiry_body_detailed' => 'تنتهي رخصتك خلال :days يوم. يرجى التواصل مع الإدارة.',
    'notification_pickup_reminder_title' => 'تذكير بالاستلام',
    'notification_pickup_reminder_body' => 'رقم الشحنة: :awb - الاستلام غداً من :area',
    'notification_pickup_reminder_body_detailed' => 'رقم الشحنة: :awb - الاستلام غداً في :date من :area',
    'notification_pending_acceptance_title' => 'شحنة في انتظار القبول',
    'notification_pending_acceptance_body' => 'الشحنة رقم #:id لا تزال في انتظار قبولك',
];
