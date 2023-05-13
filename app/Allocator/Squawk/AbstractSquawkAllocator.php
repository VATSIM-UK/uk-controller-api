<?php

namespace App\Allocator\Squawk;

use App\Models\Squawk\AbstractSquawkRange;
use App\Models\Squawk\Reserved\NonAssignableSquawkCode;
use App\Models\Squawk\SquawkAssignment;
use App\Services\NetworkAircraftService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;

abstract class AbstractSquawkAllocator implements SquawkAllocatorInterface
{
    private readonly array $nonAssignableCodes;

    final public function allocate(string $callsign, array $details): ?SquawkAssignmentInterface
    {
        // Check if the allocator can actually allocate a squawk
        if (!$this->canAllocateSquawk($details)) {
            return null;
        }

        NetworkAircraftService::createPlaceholderAircraft($callsign);

        // Loop the possible squawk ranges and possible codes and try to assign one
        foreach ($this->getPossibleSquawkRanges($details) as $range) {
            foreach ($range->getAllSquawksInRange()->shuffle() as $potentialCode) {
                if ($assignment = $this->tryAssignSquawk($callsign, $potentialCode)) {
                    return $assignment;
                }
            }
        }

        return null;
    }

    /**
     * Return all the possible squawk ranges that we're able to allocate from.
     *
     * @return Collection | AbstractSquawkRange[]
     */
    private function getPossibleSquawkRanges(array $details): Collection
    {
        return $this->filterRanges(
            $this->getOrderedSquawkRangesQuery($details)
                ->inRandomOrder()
                ->get(),
            $details
        );
    }

    private function tryAssignSquawk($callsign, $code): ?SquawkAssignmentInterface
    {
        if ($this->codeIsNotAssignable($code)) {
            return null;
        }

        try {
            return SquawkAssignment::updateOrCreate(
                ['callsign' => $callsign],
                [
                    'callsign' => $callsign,
                    'code' => $code,
                    'assignment_type' => $this->getAssignmentType(),
                ],
            );
        } catch (QueryException $queryException) {
            if ($queryException->errorInfo[1] !== 1062) {
                throw $queryException;
            }

            return null;
        }
    }

    protected function filterRanges(Collection $ranges, array $details): Collection
    {
        // By default, don't apply any filtering
        return $ranges;
    }

    /**
     * Check if the code is non-assignable.
     *
     * There are only a small handful of non-assignable codes (and they won't change much), so we can
     * load them into memory once.
     */
    private function codeIsNotAssignable(string $code): bool
    {
        if (!$this->nonAssignableCodes) {
            $this->nonAssignableCodes = NonAssignableSquawkCode::all()
                ->pluck('code')
                ->mapWithKeys(fn (string $code) => [$code => $code]);
        }

        return array_key_exists($code, $this->nonAssignableCodes);
    }

    /**
     * Returns a query builder object, with ordering where applicable, that when executed
     * will return a collection of possible ranges that the squawk can be assigned from.
     */
    abstract protected function getOrderedSquawkRangesQuery(array $details): Builder;

    /**
     * Returns whether the particular allocator can allocate a squawk, given details
     * provided.
     */
    abstract protected function canAllocateSquawk(array $details): bool;

    /**
     * Returns the type of assignment made by this allocator so it can be recorded and audited.
     */
    abstract protected function getAssignmentType(): string;
}
