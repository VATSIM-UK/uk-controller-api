<?php

namespace App\Console\Commands;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\SquawkService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class ReclaimSquawksAfterFirExit extends Command
{
    protected $signature = 'squawks:reclaim';

    protected $description = 'Reclaim squawk codes where the aircraft has left the UK';

    private $squawkService;

    public function __construct(SquawkService $squawkService)
    {
        parent::__construct();
        $this->squawkService = $squawkService;
    }

    /**
     * Run the command

     * @return integer
     */
    public function handle()
    {
        $this->info('Reclaiming squawk codes');
        NetworkAircraft::with('firEvents', 'firEvents.flightInformationRegion')
            ->whereHas('firEvents.flightInformationRegion', function (Builder $query) {
                $query->whereIn('identification_code', ['EGTT', 'EGPX']);
            })
            ->each(function (NetworkAircraft $aircraft) {

                $lastExit = $aircraft->firEvents->where('event_type', 'FIR_EXIT')
                    ->sortByDesc('created_at')
                    ->first();

                if (!$lastExit) {
                    return;
                }

                $lastEntry = $aircraft->firEvents->where('event_type', 'FIR_ENTRY')
                    ->where('created_at', '>=', $lastExit->created_at)
                    ->sortByDesc('created_at')
                    ->first();

                // If the aircraft is still in an FIR
                if ($lastEntry) {
                    return;
                }

                if ($lastExit->created_at < Carbon::now()->subHours(1)) {
                    $this->squawkService->deleteSquawkAssignment($aircraft->callsign);
                    Log::info('Reclaimed squawk for ' . $aircraft->callsign);
                }
            });
        $this->info('Finished reclaiming squawk codes');
        return 0;
    }
}
