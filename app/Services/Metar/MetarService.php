<?php

namespace App\Services\Metar;

use App\Events\MetarsUpdatedEvent;
use App\Models\Airfield\Airfield;
use App\Models\Metars\Metar;
use App\Services\Metar\Parser\MetarParser;
use Illuminate\Support\Collection;
use Psr\Log\LoggerInterface;

class MetarService
{
    private MetarRetrievalService $retrievalService;

    /**
     * @var Collection|MetarParser[]
     */
    private Collection $parsers;

    private LoggerInterface $logger;

    public function __construct(MetarRetrievalService $retrievalService, Collection $parsers, LoggerInterface $logger)
    {
        $this->retrievalService = $retrievalService;
        $this->parsers = $parsers;
        $this->logger = $logger;
    }

    public function updateAllMetars(): void
    {
        try {
            $startTime = microtime(true);
            $metarAirfields = Airfield::all();

            $updatedMetars = $this->getUpdatedMetars($metarAirfields);
            if ($updatedMetars->isEmpty()) {
                $this->logger->info('METAR update: No updated METARs to process');
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

            $duration = round(microtime(true) - $startTime, 2);
            $this->logger->info("METAR update: Updated {$updatedMetars->count()} METARs in {$duration}s");
        } catch (\Exception $e) {
            $this->logger->error('METAR update failed: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    private function getUpdatedMetars(Collection $airfields): Collection
    {
        try {
            $currentMetars = Metar::with('airfield')->get()->mapWithKeys(function (Metar $metar) {
                return [$metar->airfield->code => $metar->raw];
            });

            $newMetars = $this->getMetarsForAirfields($airfields);

            return $newMetars->reject(
                function (DownloadedMetar $metar, string $airfield) use ($currentMetars) {
                    return $currentMetars->offsetExists($airfield) && $metar->raw() === $currentMetars->offsetGet(
                        $airfield
                    );
                }
            );
        } catch (\Exception $e) {
            $this->logger->error('METAR update: Error in getUpdatedMetars: ' . $e->getMessage());
            throw $e;
        }
    }

    private function getMetarsForAirfields(Collection $airfields): Collection
    {
        try {
            return $this->retrievalService->retrieveMetars($airfields->pluck('code'));
        } catch (\Exception $e) {
            $this->logger->error('METAR update: Error retrieving METARs: ' . $e->getMessage());
            throw $e;
        }
    }

    private function getUpsertMetarData(Collection $airfields, Collection $metars): array
    {
        try {
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
        } catch (\Exception $e) {
            $this->logger->error('METAR update: Error building upsert data: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getParsers(): Collection
    {
        return $this->parsers;
    }
}
