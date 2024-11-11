<?php

namespace App\Providers;

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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Page::$reportValidationErrorUsing = function (ValidationException $exception) {

        //     if($exception->getMessage() == "Truck Weight is not enough"){
        //         Notification::make()
        //         ->title($exception->getMessage())
        //         ->danger()
        //         ->send();
        //     }

        // };

        FilamentColor::register([
            'PENDING' => Color::Gray, // Blue
            'CANCELLED' => Color::Red, // Red
            'ASSIGNEDMISSION' => Color::Yellow, // Yellow
            'DRIVER_RECEIVED' => Color::Purple, // Green
            'ON_ITS_WAY' => Color::Sky, // Orange
        ]);
    }
}
