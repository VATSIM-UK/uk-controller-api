<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use Closure;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

trait SelectsStandsUsingStandardConditions
{
    use SelectsFromSizeAppropriateAvailableStands;
    use OrdersStandsByCommonConditions;
    use AppliesOrdering;
    use SelectsFirstApplicableStand;

    private function selectStandsUsingStandardConditions(
        NetworkAircraft $aircraft,
        Closure $specificFilters,
        array $specificOrders = [],
        bool $includeAssignmentPriority = true
    ): ?int {
        return $this->selectFirstStand(
            $this->standardConditionsStandQuery(
                $aircraft,
                $specificFilters,
                $specificOrders,
                $includeAssignmentPriority,
                false
            )
        );
    }

    private function selectRankedStandsUsingStandardConditions(
        NetworkAircraft $aircraft,
        Closure $specificFilters,
        array $specificOrders = [],
        bool $includeAssignmentPriority = true
    ): Collection {
        $orderByForRankQuery = implode(
            ',',
            $this->orderByForStandsQuery($aircraft, $specificOrders, $includeAssignmentPriority, true)
        );

        return $this->standardConditionsStandQuery(
            $aircraft,
            $specificFilters,
            $specificOrders,
            $includeAssignmentPriority,
            true
        )
            ->selectRaw(sprintf('DENSE_RANK() OVER (ORDER BY %s) AS `rank`', $orderByForRankQuery))
            ->get();
    }

    private function standardConditionsStandQuery(
        NetworkAircraft $aircraft,
        Closure $specificFilters,
        array $specificOrders = [],
        bool $includeAssignmentPriority = true,
        bool $isRanking = false
    ): Builder {
        return $this->applyOrderingToStandsQuery(
            $this->joinOtherStandRequests(
                $specificFilters(
                    $isRanking
                    ? $this->sizeAppropriateAvailableStandsAtAirfieldForRanking($aircraft)
                    : $this->sizeAppropriateAvailableStandsAtAirfield($aircraft)
                ),
                $aircraft
            ),
            $this->orderByForStandsQuery($aircraft, $specificOrders, $includeAssignmentPriority, $isRanking)
    /**
     * Build stand ordering for a given aircraft/query mode.
     */
    private function orderByForStandsQuery(
        NetworkAircraft $aircraft,
        array $customOrders,
        bool $includeAssignmentPriority,
        bool $isRanking
    ): array {
        /**
         * If we are doing ranking, we don't need to consider stand requests in the priority, nor do we need
         * a random order.
         */
        if ($includeAssignmentPriority) {
            $commonConditions = $isRanking
                ? $this->commonOrderByConditionsForRanking()
                : $this->commonOrderByConditionsForAircraft($aircraft);
        } else {
            $commonConditions = $isRanking
                ? $this->commonOrderByConditionsWithoutAssignmentPriorityForRanking()
                : $this->commonOrderByConditionsWithoutAssignmentPriorityForAircraft($aircraft);
        }

        $nightTimeRemoteStandCondition = $this->nightTimeRemoteStandOrderCondition();

        if ($nightTimeRemoteStandCondition !== null) {
            $commonConditions = array_merge([$nightTimeRemoteStandCondition], $commonConditions);
        }

        return array_merge(
            $customOrders,
            $commonConditions
        );
    }

    /**
     * Returns an optional SQL ORDER BY fragment that prefers remote stands overnight
     * within the configured night window, or null when the bias should not be applied.
     */
    private function nightTimeRemoteStandOrderCondition(): ?string
    {
        $config = config('stands.night_remote_stand_weighting');

        $hour = Carbon::now('Europe/London')->hour;
        $startHour = (int) ($config['start_hour'] ?? 22);
        $endHour = (int) ($config['end_hour'] ?? 6);

        // Supports both normal windows (e.g. 01 -> 05) and windows crossing midnight (e.g. 22 -> 06).
        $isNightWindow = $startHour <= $endHour
            ? $hour >= $startHour && $hour < $endHour
            : $hour >= $startHour || $hour < $endHour;

        if (!$isNightWindow) {
            return null;
        }

        return 'CASE WHEN stands.overnight_remote_preferred = 1 THEN 0 ELSE 1 END ASC';
    }

    private function commonOrderByConditionsForAircraft(NetworkAircraft $aircraft): array
    {
        return $aircraft->cid === null
            ? $this->commonOrderByConditionsWithoutRequests()
            : $this->commonOrderByConditions();
    }

    private function commonOrderByConditionsWithoutAssignmentPriorityForAircraft(NetworkAircraft $aircraft): array
    {
        return $aircraft->cid === null
            ? $this->commonOrderByConditionsWithoutRequestsOrAssignmentPriority()
            : $this->commonOrderByConditionsWithoutAssignmentPriority();
    }
}
