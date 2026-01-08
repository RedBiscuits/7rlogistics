<?php

namespace App\Providers;

use App\Http\Responses\LogoutResponse;
use App\Models\Shipment;
use App\Observers\ShipmentObserver;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Support\ServiceProvider;

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
