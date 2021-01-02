<?php

namespace App\Services;

use App\Models\Departure\DepartureInterval;
use App\Models\Departure\DepartureIntervalType;
use App\Models\Sid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class DepartureIntervalService
{
    /**
     * Create an MDI
     */
    public function createMinimumDepartureInterval(
        int $interval,
        string $airfield,
        array $sids,
        Carbon $expiresAt
    ) : DepartureInterval {
        $interval = $this->createDepartureInterval($interval, 'mdi', $expiresAt);
        $this->addSidToDepartureInterval($interval, $airfield, $sids);
        return $interval;
    }

    private function createDepartureInterval(int $interval, string $type, Carbon $expiresAt): DepartureInterval
    {
        return DepartureInterval::create(
            [
                'interval' => $interval,
                'type_id' => DepartureIntervalType::where('key', $type)->first()->id,
                'expires_at' => $expiresAt
            ]
        );
    }

    /**
     * Associate sids with a newly created departure interval
     */
    private function addSidToDepartureInterval(DepartureInterval $interval, string $airfield, array $identifiers): void
    {
        $sids = Sid::whereHas('airfield', function (Builder $airfieldQuery) use ($airfield) {
            $airfieldQuery->where('code', $airfield);
        })
            ->whereIn('identifier', $identifiers)
            ->pluck('id');

        $interval->sids()->sync($sids);
    }
}
