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
            $this->logger->info('METAR update: Starting to fetch all airfields');
            $startTime = microtime(true);
            $metarAirfields = Airfield::all();
            $this->logger->info('METAR update: Fetched ' . $metarAirfields->count() . ' airfields in ' . round(microtime(true) - $startTime, 2) . 's');

            $startTime = microtime(true);
            $this->logger->info('METAR update: Starting to fetch updated METARs');
            $updatedMetars = $this->getUpdatedMetars($metarAirfields);
            $this->logger->info('METAR update: Fetched ' . $updatedMetars->count() . ' updated METARs in ' . round(microtime(true) - $startTime, 2) . 's');

            if ($updatedMetars->isEmpty()) {
                $this->logger->info('METAR update: No updated METARs to process, exiting');
                return;
            }

            $startTime = microtime(true);
            $this->logger->info('METAR update: Starting to prepare upsert data');
            $upsertData = $this->getUpsertMetarData($metarAirfields, $updatedMetars);
            $this->logger->info('METAR update: Prepared ' . count($upsertData) . ' records for upsert in ' . round(microtime(true) - $startTime, 2) . 's');

            $startTime = microtime(true);
            $this->logger->info('METAR update: Starting upsert operation');
            Metar::upsert(
                $upsertData,
                ['airfield_id']
            );
            $this->logger->info('METAR update: Upsert completed in ' . round(microtime(true) - $startTime, 2) . 's');

            $startTime = microtime(true);
            $this->logger->info('METAR update: Firing MetarsUpdatedEvent');
            event(
                new MetarsUpdatedEvent(
                    Metar::with('airfield')
                        ->whereIn('airfield_id', array_column($upsertData, 'airfield_id'))
                        ->get()
                )
            );
            $this->logger->info('METAR update: MetarsUpdatedEvent fired in ' . round(microtime(true) - $startTime, 2) . 's');
        } catch (\Exception $e) {
            $this->logger->error('METAR update failed: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    private function getUpdatedMetars(Collection $airfields): Collection
    {
        try {
            $this->logger->info('METAR update: Fetching current METARs from database');
            $startTime = microtime(true);
            $currentMetars = Metar::with('airfield')->get()->mapWithKeys(function (Metar $metar) {
                return [$metar->airfield->code => $metar->raw];
            });
            $this->logger->info('METAR update: Loaded ' . $currentMetars->count() . ' current METARs in ' . round(microtime(true) - $startTime, 2) . 's');

            $this->logger->info('METAR update: Retrieving new METARs from service');
            $startTime = microtime(true);
            $newMetars = $this->getMetarsForAirfields($airfields);
            $this->logger->info('METAR update: Retrieved ' . $newMetars->count() . ' new METARs in ' . round(microtime(true) - $startTime, 2) . 's');

            $this->logger->info('METAR update: Filtering for changes');
            return $newMetars->reject(
                function (DownloadedMetar $metar, string $airfield) use ($currentMetars) {
                    return $currentMetars->offsetExists($airfield) && $metar->raw() === $currentMetars->offsetGet(
                        $airfield
                    );
                }
            );
        } catch (\Exception $e) {
            $this->logger->error('METAR update: Error in getUpdatedMetars: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    private function getMetarsForAirfields(Collection $airfields): Collection
    {
        try {
            $this->logger->info('METAR update: Retrieving METARs for ' . $airfields->count() . ' airfields');
            $startTime = microtime(true);
            $result = $this->retrievalService->retrieveMetars($airfields->pluck('code'));
            $this->logger->info('METAR update: Retrieved METARs in ' . round(microtime(true) - $startTime, 2) . 's');
            return $result;
        } catch (\Exception $e) {
            $this->logger->error('METAR update: Error retrieving METARs: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    private function getUpsertMetarData(Collection $airfields, Collection $metars): array
    {
        try {
            $this->logger->info('METAR update: Building upsert data for ' . $metars->count() . ' updated METARs');
            $startTime = microtime(true);

            $result = $airfields->filter(function (Airfield $airfield) use ($metars) {
                return $metars->has($airfield->code);
            })
                ->map(function (Airfield $airfield) use ($metars) {
                    $this->logger->debug('METAR update: Processing airfield ' . $airfield->code);
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

            $this->logger->info('METAR update: Built upsert data in ' . round(microtime(true) - $startTime, 2) . 's with ' . count($result) . ' records');
            return $result;
        } catch (\Exception $e) {
            $this->logger->error('METAR update: Error building upsert data: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    public function getParsers(): Collection
    {
        return $this->parsers;
    }
}
