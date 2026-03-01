<?php

namespace App\Console\Commands;

use App\Imports\Stand\StandReservationsImport;
use App\Models\Stand\StandReservationPlan;
use App\Services\Stand\StandReservationPayloadRowsBuilder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ActivateStandReservationPlans extends Command
{
    protected $signature = 'stand-reservations:activate-plans';

    protected $description = 'Import due stand reservation plans';

    public function handle(
        StandReservationsImport $importer,
        StandReservationPayloadRowsBuilder $payloadRowsBuilder
    ): int {
        StandReservationPlan::query()
            ->where('status', 'pending')
            ->get()
            ->each(function (StandReservationPlan $plan): void {
                $eventStart = $plan->eventStartAt();

                if ($eventStart !== null && $eventStart->isPast()) {
                    $plan->update([
                        'status' => 'denied',
                        'denied_at' => Carbon::now(),
                        'denied_by' => null,
                    ]);
                }
            });

        $plans = StandReservationPlan::query()
            ->where('status', 'approved')
            ->whereNull('imported_reservations')
            ->orderBy('approved_at')
            ->get();

        $activated = 0;

        foreach ($plans as $plan) {
            $payload = $plan->payload ?? [];
            $eventStart = $plan->eventStartAt();

            if ($eventStart === null || $eventStart->isFuture()) {
                continue;
            }

            $createdReservations = $importer->importReservations(
                $payloadRowsBuilder->fromPayload($payload)
            );

            $plan->update([
                'imported_reservations' => $createdReservations,
            ]);

            $activated++;
        }

        $this->info(sprintf('Activated %d stand reservation plan(s).', $activated));

        return 0;
    }
}
