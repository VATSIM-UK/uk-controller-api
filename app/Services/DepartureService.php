<?php

namespace App\Services;

use App\Events\DepartureRestrictionUpdatedEvent;
use App\Models\Aircraft\RecatCategory;
use App\Models\Aircraft\WakeCategory;
use App\Models\Departure\DepartureRestriction;
use App\Models\Departure\DepartureRestrictionType;
use App\Models\Departure\SidDepartureIntervalGroup;
use App\Models\Sid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class DepartureService
{
    public function getActiveRestrictions(): Collection
    {
        return DepartureRestriction::with('sids', 'sids.airfield')->active()->get();
    }

    /**
     * Create an MDI
     */
    public function createMinimumDepartureInterval(
        int $interval,
        string $airfield,
        array $sids,
        Carbon $expiresAt
    ) : DepartureRestriction {
        return $this->createDepartureRestriction($interval, 'mdi', $airfield, $sids, $expiresAt);
    }

    /**
     * Create an MDI
     */
    public function createAverageDepartureInterval(
        int $interval,
        string $airfield,
        array $sids,
        Carbon $expiresAt
    ) : DepartureRestriction {
        return $this->createDepartureRestriction($interval, 'adi', $airfield, $sids, $expiresAt);
    }

    public function updateDepartureRestriction(
        int $id,
        int $interval,
        string $airfield,
        array $sids,
        Carbon $expiresAt
    ) : DepartureRestriction {
        return tap(
            DepartureRestriction::findOrFail($id),
            function (DepartureRestriction $model) use ($interval, $airfield, $sids, $expiresAt) {
                $model->update(['expires_at' => $expiresAt, 'interval' => $interval]);
                $this->addSidToDepartureRestriction($model, $airfield, $sids);
                $this->triggerRestrictionUpdatedEvent($model);
            }
        );
    }

    public function expireDepartureRestriction(int $id): void
    {
        $this->triggerRestrictionUpdatedEvent(DepartureRestriction::findOrFail($id)->expire());
    }

    public function triggerRestrictionUpdatedEvent(DepartureRestriction $interval): void
    {
        event(new DepartureRestrictionUpdatedEvent($interval));
    }

    public function getDepartureUkWakeIntervalsDependency(): array
    {
        $mappings = [];
        $categories = WakeCategory::with('departureIntervals', 'scheme')
            ->whereHas('departureIntervals')
            ->whereHas('scheme', function (Builder $scheme) {
                $scheme->uk();
            })
            ->get();

        foreach ($categories as $category) {
            foreach ($category->departureIntervals as $follow) {
                $mappings[] = [
                    'lead' => $category->code,
                    'follow' => $follow->code,
                    'interval' => (int) $follow->pivot->interval,
                    'intermediate' => (bool) $follow->pivot->intermediate,
                ];
            }
        }

        return $mappings;
    }

    public function getDepartureRecatWakeIntervalsDependency(): array
    {
        $mappings = [];
        $categories = WakeCategory::with('departureIntervals', 'scheme')
            ->whereHas('departureIntervals')
            ->whereHas('scheme', function (Builder $scheme) {
                $scheme->recat();
            })
            ->get();

        foreach ($categories as $category) {
            foreach ($category->departureIntervals as $follow) {
                $mappings[] = [
                    'lead' => $category->code,
                    'follow' => $follow->code,
                    'interval' => (int) $follow->pivot->interval
                ];
            }
        }

        return $mappings;
    }

    public function getDepartureIntervalGroupsDependency(): array
    {
        return SidDepartureIntervalGroup::with('relatedGroups')->get()->toArray();
    }

    /**
     * Create a specified type of departure restriction
     */
    private function createDepartureRestriction(
        int $interval,
        string $type,
        string $airfield,
        array $sids,
        Carbon $expiresAt
    ): DepartureRestriction {
        return tap(
            DepartureRestriction::create(
                [
                    'interval' => $interval,
                    'type_id' => DepartureRestrictionType::where('key', $type)->first()->id,
                    'expires_at' => $expiresAt
                ]
            ),
            function (DepartureRestriction $interval) use ($airfield, $sids){
                $this->addSidToDepartureRestriction($interval, $airfield, $sids);
                $this->triggerRestrictionUpdatedEvent($interval);
            }
        );
    }

    /**
     * Associate sids with a newly created departure interval
     */
    private function addSidToDepartureRestriction(
        DepartureRestriction $restriction,
        string $airfield,
        array $identifiers
    ): void {
        $sids = Sid::whereHas('airfield', function (Builder $airfieldQuery) use ($airfield) {
            $airfieldQuery->where('code', $airfield);
        })
            ->whereIn('identifier', $identifiers)
            ->pluck('id');

        $restriction->sids()->sync($sids);
    }
}
