<?php

namespace App\Services;

use App\Helpers\Vatsim\ControllerPositionParser;
use App\Models\Controller\ControllerPosition;
use App\Models\Controller\ControllerPositionAlternativeCallsign;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ControllerService
{
    private ControllerPositionParser $controllerPositionParser;

    public function __construct(ControllerPositionParser $controllerPositionParser)
    {
        $this->controllerPositionParser = $controllerPositionParser;
    }

    public function getControllerPositionsDependency(): Collection
    {
        return ControllerPosition::with('topDownAirfields')->orderBy('id')->get()->map(
            function (ControllerPosition $position) {
                return [
                    'id' => $position->id,
                    'callsign' => $position->callsign,
                    'frequency' => (float) $position->frequency,
                    'top_down' => $position->topDownAirfields->pluck('code')->toArray(),
                    'requests_departure_releases' => $position->requests_departure_releases,
                    'receives_departure_releases' => $position->receives_departure_releases,
                    'sends_prenotes' => $position->sends_prenotes,
                    'receives_prenotes' => $position->receives_departure_releases,
                ];
            }
        );
    }

    public function getTopDownPositionsForPosition(string $callsign): Collection
    {
        $position = ControllerPosition::fromCallsign($callsign);

        $positionIds = DB::table('top_downs as target')
            ->join('top_downs as below', function ($join) {
                $join->on('below.airfield_id', '=', 'target.airfield_id')
                    ->whereColumn('below.order', '<', 'target.order');
            })
            ->where('target.controller_position_id', $position->id)
            ->orderByDesc('below.order')
            ->orderBy('below.controller_position_id')
            ->pluck('below.controller_position_id')
            ->unique()
            ->values();

        $positions = ControllerPosition::whereIn('id', $positionIds)->get()->keyBy('id');

        return $positionIds->map(fn ($id) => $positions->get($id))->filter()->values();
    }

    public static function getControllerLevelFromCallsign(string $callsign): string
    {
        return strtoupper(strpos($callsign, '_') === false ? '' : substr($callsign, strrpos($callsign, '_') + 1));
    }

    public static function getControllerFacilityFromCallsign(string $callsign): string
    {
        return strtoupper(strpos($callsign, '_') === false ? $callsign : substr($callsign, 0, strpos($callsign, '_')));
    }

    /**
     * Returns all the possible parsed controller positions, keyed by the position id.
     *
     * @return Collection|Collection[]
     */
    public function getParsedControllerPositionsWithAlternatives(): Collection
    {
        return ControllerPosition::with('alternativeCallsigns')->get()->mapWithKeys(function (ControllerPosition $position) {
            $parsedPosition = $this->controllerPositionParser->parse($position);
            if ($parsedPosition === null) {
                Log::error(sprintf('Invalid controller position when parsed: %s/%d', $position->callsign, $position->id));

                return [$position->id => null];
            }

            return [
                $position->id => collect([$parsedPosition])->concat(
                    $position->alternativeCallsigns->map(function (ControllerPositionAlternativeCallsign $alternativeCallsign) use ($position) {
                        $position->callsign = $alternativeCallsign->callsign;

                        return $this->controllerPositionParser->parse($position);
                    })
                )->filter(),
            ];
        })->filter();
    }
}
