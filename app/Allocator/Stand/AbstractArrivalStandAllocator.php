<?php

namespace App\Allocator\Stand;

use App\Models\Aircraft\Aircraft;
use App\Models\Stand\Stand;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;

abstract class AbstractArrivalStandAllocator implements ArrivalStandAllocatorInterface
{
    final public function allocate(NetworkAircraft $aircraft): ?int
    {
        return $this->getPossibleStands($aircraft)->first()?->id;
    }

    /*
     * Base query for stands at the arrival airfield, which are of a suitable
     * size (or max size if no type) for the aircraft and not occupied.
     */
    private function getArrivalAirfieldStandQuery(NetworkAircraft $aircraft): Builder
    {
        return Stand::whereHas('airfield', function (Builder $query) use ($aircraft) {
            $query->where('code', $aircraft->planned_destairport);
        })
            ->sizeAppropriate(Aircraft::where('code', $aircraft->planned_aircraft_short)->first())
            ->available()
            ->select('stands.*');
    }

    /**
     * Get all the possible stands that are available for allocation.
     *
     * @param NetworkAircraft $aircraft
     * @return Collection|Stand[]
     */
    private function getPossibleStands(NetworkAircraft $aircraft): Collection
    {
        $orderedQuery = $this->getOrderedStandsQuery($this->getArrivalAirfieldStandQuery($aircraft), $aircraft);
        return $orderedQuery === null
            ? new Collection()
            : $this->applyBaseOrderingToStandsQuery($orderedQuery, $aircraft)->get();
    }

    /**
     * Apply the base ordering to the stands query. This orders stands by weight ascending
     * so smaller aircraft prefer smaller stands and also applies an element of randomness
     * so we don't just put all the aircraft next to each other.
     *
     * @param Builder $query
     * @return Builder
     */
    private function applyBaseOrderingToStandsQuery(Builder $query, NetworkAircraft $aircraft): Builder
    {
        return $query->orderByAerodromeReferenceCode()
            ->orderByAssignmentPriority()
            ->leftJoin('stand_requests as other_stand_requests', function (JoinClause $join) use ($aircraft) {
                // Prefer stands that haven't been requested by someone else
                $join->on('stands.id', '=', 'other_stand_requests.stand_id')
                    ->on('other_stand_requests.user_id', '<>', $join->raw($aircraft->cid))
                    ->on(
                        'other_stand_requests.requested_time',
                        '>',
                        $join->raw(
                            sprintf(
                                '\'%s\'',
                                Carbon::now()
                            )
                        )
                    );
            })
            ->orderByRaw('other_stand_requests.id IS NULL')
            ->inRandomOrder();
    }

    /**
     * If true, will prefer stands that haven't been requsted by the user;
     */
    protected function prefersNonRequestedStands(): bool
    {
        return true;
    }

    abstract protected function getOrderedStandsQuery(Builder $stands, NetworkAircraft $aircraft): ?Builder;
}
