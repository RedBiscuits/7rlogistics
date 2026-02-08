<?php

namespace App\Providers;

use App\Filament\Super_Admin\Resources\AttendanceResource\Pages\CreateAttendance;
use App\Filament\Super_Admin\Resources\AttendanceResource\Pages\EditAttendance;
use App\Filament\Super_Admin\Resources\AttendanceResource\Pages\ListAttendances;
use App\Filament\Super_Admin\Resources\AttendanceResource\Pages\ViewAttendance;
use App\Http\Responses\LogoutResponse;
use App\Models\Shipment;
use App\Observers\ShipmentObserver;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Explicit Livewire component names for Super_Admin Attendance resource pages
        // so production resolves the component (avoids "Unable to find component" when
        // namespace-derived name differs, e.g. super_-admin vs super_admin).
        $adminPrefix = 'app.filament.super_-admin.resources.attendance-resource.pages.';
        Livewire::component($adminPrefix . 'list-attendances', ListAttendances::class);
        Livewire::component($adminPrefix . 'create-attendance', CreateAttendance::class);
        Livewire::component($adminPrefix . 'view-attendance', ViewAttendance::class);
        Livewire::component($adminPrefix . 'edit-attendance', EditAttendance::class);
        FilamentColor::register([
            'PENDING' => Color::Gray, // Blue
            'CANCELLED' => Color::Red, // Red
            'ASSIGNEDMISSION' => Color::Yellow, // Yellow
            'DRIVER_RECEIVED' => Color::Purple, // Green
            'ON_ITS_WAY' => Color::Sky, // Orange
        ]);

        // Register observers
        Shipment::observe(ShipmentObserver::class);
    }
}
