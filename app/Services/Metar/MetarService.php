<?php

namespace App\Services\Metar;

use App\Events\MetarsUpdatedEvent;
use App\Models\Airfield\Airfield;
use App\Models\Metars\Metar;
use App\Services\Metar\Parser\MetarParser;
use Illuminate\Support\Collection;

class MetarService
{
    private MetarRetrievalService $retrievalService;

    /**
     * @var MetarParser[]
     */
    private Collection $parsers;

    public function __construct(MetarRetrievalService $retrievalService, Collection $parsers)
    {
        $this->retrievalService = $retrievalService;
        $this->parsers = $parsers;
    }

    public function updateAllMetars(): void
    {
        $metarAirfields = Airfield::all();
        $metarData = $this->retrievalService->retrieveMetars($metarAirfields->pluck('code'));
        if ($metarData->isEmpty()) {
            return;
        }

        Metar::upsert(
            $this->getUpsertMetarData($metarAirfields, $metarData),
            ['airfield_id']
        );

        event(new MetarsUpdatedEvent(Metar::with('airfield')->get()));
    }

    private function getUpsertMetarData(Collection $airfields, Collection $metars): array
    {
        return $airfields->filter(function (Airfield $airfield) use ($metars) {
            return $metars->has($airfield->code);
        })
            ->map(function (Airfield $airfield) use ($metars) {
                $metar = $metars[$airfield->code];

                return [
                    'airfield_id' => $airfield->id,
                    'raw' => $metar->implode(' '),
                    'parsed' => $this->parsers->reduce(
                        function (Collection $parsed, MetarParser $parser) use ($airfield, $metar) {
                            return $parsed->merge($parser->parse($airfield, $metar));
                        },
                        collect()
                    ),
                ];
            })
            ->toArray();
    }

    public function getParsers(): Collection
    {
        return $this->parsers;
    }
}
