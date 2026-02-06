<?php

namespace App\Console\Commands;

use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendPendingAcceptanceReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipments:send-pending-acceptance-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send periodic reminders for shipments awaiting driver acceptance';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        $this->info('Checking for shipments awaiting driver acceptance...');

        $shipments = Shipment::whereIn('status', [
                ShipmentStatus::APPROVED,
                ShipmentStatus::ASSIGNEDMISSION,
            ])
            ->whereNotNull('driver_id')
            ->with('driver')
            ->get();

        if ($shipments->isEmpty()) {
            $this->info('No shipments awaiting acceptance found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$shipments->count()} shipment(s) awaiting acceptance.");

        $sentCount = 0;

        foreach ($shipments as $shipment) {
            if (!$shipment->driver) {
                $this->warn("Shipment #{$shipment->id} has no assigned driver. Skipping.");
                continue;
            }

            try {
                $notificationService->notifyPendingAcceptanceReminder($shipment->driver, $shipment);
                $sentCount++;
            } catch (\Throwable $e) {
                $this->error("Failed to send reminder for Shipment #{$shipment->id}: {$e->getMessage()}");
            }
        }

        $this->info("Pending acceptance reminders sent: {$sentCount}");

        return Command::SUCCESS;
    }
}

