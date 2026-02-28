<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Subject model → Filament resource (for "View resource" link)
    |--------------------------------------------------------------------------
    | Maps subject_type (model class) to the Filament resource class used to
    | view/edit that model. The resource should be in the same panel as the
    | ModelChangeNotification resource (Admin panel).
    */
    'subject_resource_map' => [
        \App\Models\Shipment::class => [
            'resource' => \App\Filament\Super_Admin\Resources\ShipmentResource::class,
            'panel' => 'admin',
        ],
        \App\Models\DriverAttendance::class => [
            'resource' => \App\Filament\Super_Admin\Resources\AttendanceResource::class,
            'panel' => 'admin',
        ],
        \App\Models\Driver::class => [
            'resource' => \App\Filament\Super_Admin\Resources\DriverResource::class,
            'panel' => 'admin',
        ],
        \App\Models\User::class => [
            'resource' => \App\Filament\Super_Admin\Resources\UserResource::class,
            'panel' => 'admin',
        ],
        \App\Models\Mission::class => [
            'resource' => \App\Filament\Super_Admin\Resources\MissionResource::class,
            'panel' => 'admin',
        ],
        \App\Models\AttendanceLocation::class => [
            'resource' => \App\Filament\Super_Admin\Resources\AttendanceLocationResource::class,
            'panel' => 'admin',
        ],
        \App\Models\TruckType::class => [
            'resource' => \App\Filament\Super_Admin\Resources\TruckTypeResource::class,
            'panel' => 'admin',
        ],
        \App\Models\ContactRequests::class => [
            'resource' => \App\Filament\Super_Admin\Resources\ContactRequestsResource::class,
            'panel' => 'admin',
        ],
        \App\Models\Company::class => [
            'resource' => \App\Filament\Super_Admin\Resources\CompanyResource::class,
            'panel' => 'admin',
        ],
        \App\Models\Area::class => [
            'resource' => \App\Filament\Super_Admin\Resources\AreaResource::class,
            'panel' => 'admin',
        ],
        \App\Models\Truck::class => [
            'resource' => \App\Filament\Super_Admin\Resources\TruckResource::class,
            'panel' => 'admin',
        ],
        \App\Models\City::class => [
            'resource' => \App\Filament\Super_Admin\Resources\CityResource::class,
            'panel' => 'admin',
        ],
    ],
];
