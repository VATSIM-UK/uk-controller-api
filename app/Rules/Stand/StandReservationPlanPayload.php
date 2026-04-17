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

        $this->validateUnknownKeys($attribute, $value, ['event_start', 'event_end', 'event_airport', 'event_airports', 'reservations'], $fail);

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

        $eventAirports = $this->validateEventAirportScope($attribute, $value, $fail);

        if (!array_key_exists('reservations', $value) || !is_array($value['reservations']) || count($value['reservations']) === 0) {
            $fail("$attribute.reservations must be a non-empty array.");
            return;
        }

        $intervalsByStand = [];

        foreach ($value['reservations'] as $index => $reservation) {
            $itemPath = "$attribute.reservations.$index";

            if (!is_array($reservation)) {
                $fail("$itemPath must be an object.");
                continue;
            }

            $this->validateUnknownKeys($itemPath, $reservation, ['stand_id', 'stand', 'airport', 'cid', 'timefrom', 'timeto'], $fail);

            $standIdProvided = array_key_exists('stand_id', $reservation) && $reservation['stand_id'] !== null;
            $standProvided = array_key_exists('stand', $reservation) && $reservation['stand'] !== null && $reservation['stand'] !== '';

            if ($standIdProvided === $standProvided) {
                $fail("$itemPath must include exactly one of stand_id or stand.");
                continue;
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

            $standKey = null;
            if ($standIdProvided) {
                if (!$this->isPositiveInteger($reservation['stand_id'])) {
                    $fail("$itemPath.stand_id must be a positive integer.");
                } else {
                    $standKey = sprintf('id:%d', $reservation['stand_id']);
                }
            }

            if ($standProvided) {
                if (!is_string($reservation['stand']) || trim($reservation['stand']) === '') {
                    $fail("$itemPath.stand must be a non-empty string.");
                }

                $resolvedAirport = $this->normalizeAirportCode($reservation['airport'] ?? null);

                if (is_null($resolvedAirport)) {
                    if (count($eventAirports) === 1) {
                        $resolvedAirport = $eventAirports[0];
                    } else {
                        $fail("$itemPath.airport is required when event_airports contains multiple airports and stand is used.");
                    }
                }

                if ($resolvedAirport && is_string($reservation['stand']) && trim($reservation['stand']) !== '') {
                    $standKey = sprintf(
                        'code:%s:%s',
                        $resolvedAirport,
                        strtoupper(trim($reservation['stand']))
                    );
                }
            }

            if ($standKey && $timeFrom && $timeTo) {
                $intervalsByStand[$standKey][] = [
                    'index' => $index,
                    'from' => $timeFrom,
                    'to' => $timeTo,
                ];
            }
        }

        $this->validateStandIntervalOverlaps($attribute, $intervalsByStand, $fail);
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

        if ($hasSingleAirport === $hasMultipleAirports) {
            $fail("$attribute must include exactly one of event_airport or event_airports.");
            return [];
        }

        if ($hasSingleAirport) {
            $airport = $this->normalizeAirportCode($payload['event_airport']);

            if (!$airport) {
                $fail("$attribute.event_airport must be a 4-letter ICAO code.");
                return [];
            }

            return [$airport];
        }

        if (!is_array($payload['event_airports']) || count($payload['event_airports']) === 0) {
            $fail("$attribute.event_airports must be a non-empty array of 4-letter ICAO codes.");
            return [];
        }

        $airports = [];
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
        if (!is_string($value) || !preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/', $value)) {
            return null;
        }

        try {
            $parsed = CarbonImmutable::createFromFormat('Y-m-d\TH:i:s\Z', $value, 'UTC');
        } catch (\Exception) {
            return null;
        }

        if (!$parsed || $parsed->format('Y-m-d\TH:i:s\Z') !== $value) {
            return null;
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
