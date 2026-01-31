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
     * @var Collection|MetarParser[]
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
        $updatedMetars = $this->getUpdatedMetars($metarAirfields);
        if ($updatedMetars->isEmpty()) {
            return;
        }

        $upsertData = $this->getUpsertMetarData($metarAirfields, $updatedMetars);
        Metar::upsert(
            $upsertData,
            ['airfield_id']
        );

        event(
            new MetarsUpdatedEvent(
                Metar::with('airfield')
                    ->whereIn('airfield_id', array_column($upsertData, 'airfield_id'))
                    ->get()
            )
        );
    }

    private function getUpdatedMetars(Collection $airfields): Collection
    {
        $currentMetars = Metar::with('airfield')->get()->mapWithKeys(function (Metar $metar) {
            return [$metar->airfield->code => $metar->raw];
        });

        return $this->getMetarsForAirfields($airfields)->reject(
            function (DownloadedMetar $metar, string $airfield) use ($currentMetars) {
                return $currentMetars->offsetExists($airfield) && $metar->raw() === $currentMetars->offsetGet(
                    $airfield
                );
            }
        );
    }

    private function getMetarsForAirfields(Collection $airfields): Collection
    {
        return $this->retrievalService->retrieveMetars($airfields->pluck('code'));
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
                    'raw' => $metar->raw(),
                    'parsed' => $this->parsers->reduce(
                        function (Collection $parsed, MetarParser $parser) use ($airfield, $metar) {
                            return $parsed->merge($parser->parse($airfield, $metar->tokenise()));
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
