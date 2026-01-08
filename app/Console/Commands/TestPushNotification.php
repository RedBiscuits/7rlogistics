<?php

namespace App\Console\Commands;

use App\Models\Driver;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class TestPushNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:push-notification 
                            {driver_id : The ID of the driver to send notification to}
                            {--title=Test Notification : The notification title}
                            {--body=This is a test push notification : The notification body}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test push notification to a driver';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        $driverId = $this->argument('driver_id');
        $title = $this->option('title');
        $body = $this->option('body');

        $driver = Driver::find($driverId);

        if (!$driver) {
            $this->error("Driver with ID {$driverId} not found.");
            return Command::FAILURE;
        }

        $this->info("Sending test notification to Driver #{$driver->id} ({$driver->code})...");

        // Check if driver has registered devices
        $activeDevices = $driver->devices()->active()->count();
        
        if ($activeDevices === 0) {
            $this->warn("⚠️  Driver has no active devices registered.");
            $this->info("To register a device, use the /auth/register-device endpoint with:");
            $this->line("  - device_token: Your FCM token");
            $this->line("  - device_type: 0 for Android, 1 for iOS");
            $this->newLine();
            $this->info("You can also manually register a test device:");
            $this->line("  php artisan tinker");
            $this->line("  \$driver = Driver::find({$driverId});");
            $this->line("  \$driver->devices()->create(['device_token' => 'test-token-here', 'device_type' => 0, 'is_active' => true]);");
            return Command::FAILURE;
        }

        $this->info("Found {$activeDevices} active device(s) for this driver.");

        // Send test notification
        try {
            $notificationService->sendPushNotification($driver, [
                'title' => $title,
                'body' => $body,
                'data' => [
                    'type' => 'test',
                    'timestamp' => now()->toIso8601String(),
                ],
            ]);

            $this->info("✅ Notification sent successfully!");
            $this->newLine();
            $this->info("Check the logs for details: tail -f storage/logs/laravel.log");
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("❌ Failed to send notification: " . $e->getMessage());
            $this->error("Check logs for more details: storage/logs/laravel.log");
            return Command::FAILURE;
        }
    }
}
