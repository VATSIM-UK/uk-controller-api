<?php

namespace App\Services;

use App\Exceptions\Airfield\AirfieldNotFoundException;
use App\Exceptions\Runway\RunwayHeadingInvalidException;
use App\Exceptions\Runway\RunwayIdentifierInvalidException;
use App\Exceptions\Runway\RunwayThresholdInvalidException;
use App\Helpers\Sectorfile\Coordinate;
use App\Models\Airfield\Airfield;
use App\Models\Runway\Runway;
use App\Rules\Runway\RunwayIdentifier;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Whoops\Run;

class RunwayService
{
    private const RUNWAY_IDENTIFIER_INVERSE_MAP = [
        'C' => 'C',
        'G' => 'G',
        'L' => 'R',
        'R' => 'L',
    ];

    public static function addRunwayPair(
        string $airfield,
        string $firstIdentifier,
        int $firstHeading,
        Coordinate $firstThreshold,
        string $secondIdentifier,
        int $secondHeading,
        Coordinate $secondThreshold
    ): void {
        $airfieldModel = Airfield::where('code', $airfield)
            ->first();
        if (!$airfieldModel) {
            throw AirfieldNotFoundException::fromIcao($airfield);
        }

        self::validateRunway($firstIdentifier, $firstHeading, $firstThreshold);
        self::validateRunway($secondIdentifier, $secondHeading, $secondThreshold);

        if (!self::headingsMatch($firstHeading, $secondHeading)) {
            throw RunwayHeadingInvalidException::forHeadings($firstHeading, $secondHeading);
        }

        // Create the runways, then link them as "inverse" runways
        DB::transaction(
            function () use (
                $firstIdentifier,
                $firstHeading,
                $firstThreshold,
                $secondIdentifier,
                $secondHeading,
                $secondThreshold,
                $airfieldModel
            ) {
                $first = self::createRunway($airfieldModel, $firstIdentifier, $firstHeading, $firstThreshold);
                $second = self::createRunway($airfieldModel, $secondIdentifier, $secondHeading, $secondThreshold);

                $first->inverses()->sync($second->id);
                $second->inverses()->sync($first->id);
            }
        );
    }

    private static function validateRunway(
        string $identifier,
        int $heading,
        Coordinate $threshold
    ) {
        if (!self::runwayIdentifierValid($identifier)) {
            throw RunwayIdentifierInvalidException::forIdentifier($identifier);
        }

        if (!self::headingValid($heading)) {
            throw RunwayHeadingInvalidException::forHeading($heading);
        }

        try {
            SectorfileService::coordinateFromSectorfile($threshold->getLatitude(), $threshold->getLongitude());
        } catch (Exception $exception) {
            throw RunwayThresholdInvalidException::forIdentifier($identifier, $exception->getMessage());
        }
    }

    private static function runwayIdentifierValid(string $identifier): bool
    {
        return (new RunwayIdentifier())->passes('', $identifier);
    }

    private static function headingValid(int $heading): bool
    {
        return $heading >= 0 && $heading < 360;
    }

    private static function headingsMatch(int $first, int $second): bool
    {
        return abs($first - $second) % 180 === 0;
    }

    private static function createRunway(
        Airfield $airfield,
        string $identifier,
        int $heading,
        Coordinate $threshold
    ): Runway {
        $coordinate = SectorfileService::coordinateFromSectorfile(
            $threshold->getLatitude(),
            $threshold->getLongitude()
        );
        return Runway::create(
            [
                'airfield_id' => $airfield->id,
                'identifier' => $identifier,
                'heading' => $heading,
                'threshold_latitude' => $coordinate->getLat(),
                'threshold_longitude' => $coordinate->getLng(),
                'threshold_elevation' => $airfield->elevation,
                'glideslope_angle' => 3,
            ]
        );
    }

    public function getRunwaysDependency(): array
    {
        return Runway::with('inverses')->get()->map(fn (Runway $runway) => [
            'id' => $runway->id,
            'airfield_id' => $runway->airfield_id,
            'identifier' => $runway->identifier,
            'heading' => $runway->heading,
            'threshold_latitude' => $runway->threshold_latitude,
            'threshold_longitude' => $runway->threshold_longitude,
            'threshold_elevation' => $runway->threshold_elevation,
            'inverse_runway_id' => $runway->inverses()->first() ? $runway->inverses()->first()->id : null,
            'glideslope_angle' => $runway->glideslope_angle,
        ])->toArray();
    }

    public static function inverseRunwayIdentifier(string $identifier): string
    {
        if (!self::runwayIdentifierValid($identifier)) {
            throw RunwayIdentifierInvalidException::forIdentifier($identifier);
        }

        $matches = [];
        preg_match(
            '/^(0[1-9]|[1-2]\d|3[0-6])([LCRG]?)$/',
            $identifier,
            $matches
        );

        $directionComponent = (int)$matches[1];
        $newDirectionComponent = $directionComponent === 18
            ? 36
            : ($directionComponent + 18) % 36;

        return sprintf(
            '%s%s',
            Str::padLeft($newDirectionComponent, 2, '0'),
            $matches[2] !== '' ? self::RUNWAY_IDENTIFIER_INVERSE_MAP[$matches[2]] : ''
        );
    }
}
