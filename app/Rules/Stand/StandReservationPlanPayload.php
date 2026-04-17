<?php

namespace App\Rules\Stand;

use App\Helpers\Vatsim\VatsimCidValidator;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Contracts\Validation\InvokableRule;

class StandReservationPlanPayload implements InvokableRule
{
    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail): void
    {
        if (!is_array($value)) {
            $fail("$attribute must be a JSON object.");
            return;
        }

        $this->validateUnknownKeys(
            $attribute,
            $value,
            ['event_start', 'event_end', 'event_airport', 'event_airports', 'reservations'],
            $fail
        );

        [$eventStart, $eventEnd] = $this->validateEventTimes($attribute, $value, $fail);
        $eventAirports = $this->validateEventAirportScope($attribute, $value, $fail);
        $reservations = $this->extractReservations($attribute, $value, $fail);

        if (is_null($reservations)) {
            return;
        }

        $intervalsByStand = $this->collectStandIntervals(
            $attribute,
            $reservations,
            $eventAirports,
            $eventStart,
            $eventEnd,
            $fail
        );

        $this->validateStandIntervalOverlaps($attribute, $intervalsByStand, $fail);
    }

    /**
     * @param string $attribute
     * @param array<string, mixed> $value
     * @param Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     * @return array{0:?CarbonImmutable,1:?CarbonImmutable}
     */
    private function validateEventTimes(string $attribute, array $value, Closure $fail): array
    {
        $eventStart = $this->parseZuluTime($value['event_start'] ?? null);
        if (!$eventStart) {
            $fail("$attribute.event_start must be a Zulu timestamp in format YYYY-MM-DDTHH:MM:SSZ.");
        }

        $eventEnd = $this->parseZuluTime($value['event_end'] ?? null);
        if (!$eventEnd) {
            $fail("$attribute.event_end must be a Zulu timestamp in format YYYY-MM-DDTHH:MM:SSZ.");
        }

        if ($eventStart && $eventEnd && !$eventEnd->isAfter($eventStart)) {
            $fail("$attribute.event_end must be after event_start.");
        }

        return [$eventStart, $eventEnd];
    }

    /**
     * @param string $attribute
     * @param array<string, mixed> $value
     * @param Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     * @return ?array<int, mixed>
     */
    private function extractReservations(string $attribute, array $value, Closure $fail): ?array
    {
        if (!array_key_exists('reservations', $value) || !is_array($value['reservations']) || count($value['reservations']) === 0) {
            $fail("$attribute.reservations must be a non-empty array.");
            return null;
        }

        return $value['reservations'];
    }

    /**
     * @param string $attribute
     * @param array<int, mixed> $reservations
     * @param array<int, string> $eventAirports
     * @param ?CarbonImmutable $eventStart
     * @param ?CarbonImmutable $eventEnd
     * @param Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     * @return array<string, array<int, array{index:int,from:CarbonImmutable,to:CarbonImmutable}>>
     */
    private function collectStandIntervals(
        string $attribute,
        array $reservations,
        array $eventAirports,
        ?CarbonImmutable $eventStart,
        ?CarbonImmutable $eventEnd,
        Closure $fail
    ): array {
        $intervalsByStand = [];

        foreach ($reservations as $index => $reservation) {
            $interval = $this->validateReservationAndBuildInterval(
                $attribute,
                $index,
                $reservation,
                $eventAirports,
                $eventStart,
                $eventEnd,
                $fail
            );

            if (is_null($interval)) {
                continue;
            }

            $intervalsByStand[$interval['stand_key']][] = [
                'index' => $interval['index'],
                'from' => $interval['from'],
                'to' => $interval['to'],
            ];
        }

        return $intervalsByStand;
    }

    /**
     * @param string $attribute
     * @param int $index
     * @param mixed $reservation
     * @param array<int, string> $eventAirports
     * @param ?CarbonImmutable $eventStart
     * @param ?CarbonImmutable $eventEnd
     * @param Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     * @return ?array{index:int,stand_key:string,from:CarbonImmutable,to:CarbonImmutable}
     */
    private function validateReservationAndBuildInterval(
        string $attribute,
        int $index,
        mixed $reservation,
        array $eventAirports,
        ?CarbonImmutable $eventStart,
        ?CarbonImmutable $eventEnd,
        Closure $fail
    ): ?array {
        $itemPath = "$attribute.reservations.$index";
        $interval = null;

        if (!is_array($reservation)) {
            $fail("$itemPath must be an object.");
        } else {
            $this->validateUnknownKeys($itemPath, $reservation, ['stand_id', 'stand', 'airport', 'cid', 'timefrom', 'timeto'], $fail);

            $standIdProvided = array_key_exists('stand_id', $reservation) && $reservation['stand_id'] !== null;
            $standProvided = array_key_exists('stand', $reservation) && $reservation['stand'] !== null && $reservation['stand'] !== '';
            $hasSingleStandMode = $standIdProvided !== $standProvided;

            if (!$hasSingleStandMode) {
                $fail("$itemPath must include exactly one of stand_id or stand.");
            }

            if (!$this->isValidCid($reservation['cid'] ?? null)) {
                $fail("$itemPath.cid must be a valid VATSIM CID.");
            }

            $timeFrom = $this->parseZuluTime($reservation['timefrom'] ?? null);
            if (!$timeFrom) {
                $fail("$itemPath.timefrom must be a Zulu timestamp in format YYYY-MM-DDTHH:MM:SSZ.");
            }

            $timeTo = $this->parseZuluTime($reservation['timeto'] ?? null);
            if (!$timeTo) {
                $fail("$itemPath.timeto must be a Zulu timestamp in format YYYY-MM-DDTHH:MM:SSZ.");
            }

            if ($timeFrom && $timeTo && !$timeTo->isAfter($timeFrom)) {
                $fail("$itemPath.timeto must be after timefrom.");
            }

            if ($eventStart && $timeFrom && $timeFrom->isBefore($eventStart)) {
                $fail("$itemPath.timefrom must be within the event window.");
            }

            if ($eventEnd && $timeTo && $timeTo->isAfter($eventEnd)) {
                $fail("$itemPath.timeto must be within the event window.");
            }

            $standKey = $this->resolveStandKey(
                $itemPath,
                $reservation,
                $standIdProvided,
                $standProvided,
                $eventAirports,
                $fail
            );

            if ($hasSingleStandMode && $standKey && $timeFrom && $timeTo) {
                $interval = [
                    'index' => $index,
                    'stand_key' => $standKey,
                    'from' => $timeFrom,
                    'to' => $timeTo,
                ];
            }
        }

        return $interval;
    }

    /**
     * @param string $itemPath
     * @param array<string, mixed> $reservation
     * @param bool $standIdProvided
     * @param bool $standProvided
     * @param array<int, string> $eventAirports
     * @param Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     * @return ?string
     */
    private function resolveStandKey(
        string $itemPath,
        array $reservation,
        bool $standIdProvided,
        bool $standProvided,
        array $eventAirports,
        Closure $fail
    ): ?string {
        $standKey = null;

        if ($standIdProvided) {
            if (!$this->isPositiveInteger($reservation['stand_id'])) {
                $fail("$itemPath.stand_id must be a positive integer.");
            } else {
                $standKey = sprintf('id:%d', $reservation['stand_id']);
            }
        } elseif ($standProvided) {
            $stand = $reservation['stand'];

            if (!is_string($stand) || trim($stand) === '') {
                $fail("$itemPath.stand must be a non-empty string.");
            } else {
                $resolvedAirport = $this->normalizeAirportCode($reservation['airport'] ?? null);

                if (is_null($resolvedAirport) && count($eventAirports) === 1) {
                    $resolvedAirport = $eventAirports[0];
                }

                if (is_null($resolvedAirport)) {
                    $fail("$itemPath.airport is required when event_airports contains multiple airports and stand is used.");
                } else {
                    $standKey = sprintf(
                        'code:%s:%s',
                        $resolvedAirport,
                        strtoupper(trim($stand))
                    );
                }
            }
        }

        return $standKey;
    }

    /**
     * @param string $path
     * @param array<string, mixed> $value
     * @param array<int, string> $allowedKeys
     * @param Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     * @return void
     */
    private function validateUnknownKeys(string $path, array $value, array $allowedKeys, Closure $fail): void
    {
        $unknownKeys = array_diff(array_keys($value), $allowedKeys);

        foreach ($unknownKeys as $unknownKey) {
            $fail("$path.$unknownKey is not an allowed field.");
        }
    }

    /**
     * @param string $attribute
     * @param array<string, mixed> $payload
     * @param Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     * @return array<int, string>
     */
    private function validateEventAirportScope(string $attribute, array $payload, Closure $fail): array
    {
        $hasSingleAirport = array_key_exists('event_airport', $payload);
        $hasMultipleAirports = array_key_exists('event_airports', $payload);
        $airports = [];

        if ($hasSingleAirport === $hasMultipleAirports) {
            $fail("$attribute must include exactly one of event_airport or event_airports.");
        } elseif ($hasSingleAirport) {
            $airport = $this->normalizeAirportCode($payload['event_airport']);

            if (!$airport) {
                $fail("$attribute.event_airport must be a 4-letter ICAO code.");
            } else {
                $airports = [$airport];
            }
        } else {
            if (!is_array($payload['event_airports']) || count($payload['event_airports']) === 0) {
                $fail("$attribute.event_airports must be a non-empty array of 4-letter ICAO codes.");
            } else {
                foreach ($payload['event_airports'] as $index => $airport) {
                    $normalizedAirport = $this->normalizeAirportCode($airport);
                    if (!$normalizedAirport) {
                        $fail("$attribute.event_airports.$index must be a 4-letter ICAO code.");
                        continue;
                    }

                    $airports[] = $normalizedAirport;
                }

                if (count(array_unique($airports)) !== count($airports)) {
                    $fail("$attribute.event_airports must not contain duplicate airports.");
                }
            }
        }

        return $airports;
    }

    private function normalizeAirportCode(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $airport = strtoupper(trim($value));

        if (!preg_match('/^[A-Z]{4}$/', $airport)) {
            return null;
        }

        return $airport;
    }

    private function parseZuluTime(mixed $value): ?CarbonImmutable
    {
        $parsed = null;

        if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/', $value)) {
            try {
                $candidate = CarbonImmutable::createFromFormat('Y-m-d\TH:i:s\Z', $value, 'UTC');
            } catch (\Exception) {
                $candidate = null;
            }

            if ($candidate && $candidate->format('Y-m-d\TH:i:s\Z') === $value) {
                $parsed = $candidate;
            }
        }

        return $parsed;
    }

    private function isPositiveInteger(mixed $value): bool
    {
        return is_int($value) && $value > 0;
    }

    private function isValidCid(mixed $value): bool
    {
        return is_int($value) && VatsimCidValidator::isValid($value);
    }

    /**
     * @param string $attribute
     * @param array<string, array<int, array{index:int,from:CarbonImmutable,to:CarbonImmutable}>> $intervalsByStand
     * @param Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     * @return void
     */
    private function validateStandIntervalOverlaps(string $attribute, array $intervalsByStand, Closure $fail): void
    {
        foreach ($intervalsByStand as $standIntervals) {
            usort(
                $standIntervals,
                fn (array $left, array $right): int => $left['from']->getTimestamp() <=> $right['from']->getTimestamp()
            );

            for ($i = 1; $i < count($standIntervals); $i++) {
                $previous = $standIntervals[$i - 1];
                $current = $standIntervals[$i];

                if ($current['from']->isBefore($previous['to'])) {
                    $fail(
                        sprintf(
                            '%s.reservations.%d overlaps with reservations.%d for the same stand.',
                            $attribute,
                            $current['index'],
                            $previous['index']
                        )
                    );
                }
            }
        }
    }
}
