<?php

namespace App\Console\Commands;

use App\Models\Shipment;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendPickupReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipments:send-pickup-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send pickup reminder notifications to drivers 1 day before pickup date';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        $this->info('Checking for shipments with pickup date tomorrow...');

        // Get shipments with pickup date exactly 1 day from now
        $tomorrow = now()->addDay()->startOfDay();
        $tomorrowEnd = now()->addDay()->endOfDay();

        $shipments = Shipment::whereNotNull('driver_id')
            ->whereNotNull('pickup_date')
            ->whereBetween('pickup_date', [$tomorrow, $tomorrowEnd])
            ->with(['driver', 'fromArea'])
            ->get();

        if ($shipments->isEmpty()) {
            $this->info('No shipments found with pickup date tomorrow.');
            return Command::SUCCESS;
        }

        $this->info("Found {$shipments->count()} shipment(s) with pickup date tomorrow.");

        $sentCount = 0;
        $skippedCount = 0;

        foreach ($shipments as $shipment) {
            if (!$shipment->driver) {
                $this->warn("Shipment #{$shipment->id} (AWB: {$shipment->awb}) has no assigned driver. Skipping.");
                $skippedCount++;
                continue;
            }

            try {
                $notificationService->notifyPickupReminder($shipment->driver, $shipment);
                $this->info("✓ Sent reminder for Shipment #{$shipment->id} (AWB: {$shipment->awb}) to Driver #{$shipment->driver->id} ({$shipment->driver->code})");
                $sentCount++;
            } catch (\Exception $e) {
                $this->error("✗ Failed to send reminder for Shipment #{$shipment->id}: " . $e->getMessage());
                $skippedCount++;
            }
        }

        $this->newLine();
        $this->info("Summary:");
        $this->line("  - Notifications sent: {$sentCount}");
        $this->line("  - Skipped: {$skippedCount}");

        return Command::SUCCESS;
    }
}
