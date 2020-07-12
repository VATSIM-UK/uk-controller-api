<?php

namespace App\Services;

use InvalidArgumentException;
use Location\Coordinate;

class SectorfileService
{
    const SECTORFILE_LATITUDE_REGEX = "/^([N,S])(\\d{3})\\.(\\d{2})\\.(\\d{2})\\.(\\d{3})$/";
    const SECTORFILE_LONGITUDE_REGEX = "/^([E,W])(\\d{3})\\.(\\d{2})\\.(\\d{2})\\.(\\d{3})$/";
    const MULTIPLIER_NEGATIVE = -1;
    const MULTIPLIER_POSITIVE = 1;

    public static function coordinateFromSectorfile(string $latitude, string $longitude): Coordinate
    {
        $latitudeMatches = [];
        if (preg_match(self::SECTORFILE_LATITUDE_REGEX, $latitude, $latitudeMatches) !== 1) {
            throw new InvalidArgumentException('Invalid sectorfile latitude format ' . $latitude);
        }

        $longitudeMatches = [];
        if (preg_match(self::SECTORFILE_LONGITUDE_REGEX, $longitude, $longitudeMatches) !== 1) {
            throw new InvalidArgumentException('Invalid sectorfile longitude format ' . $longitude);
        }

        // Convert and validate latitude
        $degreesLatitude = (int) ltrim($latitudeMatches[2], '0');
        $minutesLatitude = (int) ltrim($latitudeMatches[3], '0');
        $secondsLatitude = (float) ltrim(sprintf('%s.%s', $latitudeMatches[4], $latitudeMatches[5]), '0');
        static::validateLatitude($degreesLatitude, $minutesLatitude, $secondsLatitude);

        // Convert and validate longitude
        $degreesLongitude = (int) ltrim($longitudeMatches[2], '0');
        $minutesLongitude = (int) ltrim($longitudeMatches[3], '0');
        $secondsLongitude = (float) ltrim(sprintf('%s.%s', $longitudeMatches[4], $longitudeMatches[5]), '0');
        static::validateLongitude($degreesLongitude, $minutesLongitude, $secondsLongitude);

        // Work out the decimal degrees
        $latitudeDecimal = static::convertToDecimal(
            $degreesLatitude,
            $minutesLatitude,
            $secondsLatitude,
            $latitudeMatches[1] === 'S' ? static::MULTIPLIER_NEGATIVE : static::MULTIPLIER_POSITIVE
        );

        $longitudeDecimal = static::convertToDecimal(
            $degreesLongitude,
            $minutesLongitude,
            $secondsLongitude,
            $longitudeMatches[1] === 'W' ? static::MULTIPLIER_NEGATIVE : static::MULTIPLIER_POSITIVE
        );

        return new Coordinate($latitudeDecimal, $longitudeDecimal);
    }

    private static function validateLatitude(int $degrees, int $minutes, float $seconds): void
    {
        if ($degrees === 90 && ($minutes !== 0 || $seconds !== 0.0)) {
            throw new InvalidArgumentException('Cannot have more than 90 degrees of latitude');
        } elseif ($degrees > 90) {
            throw new InvalidArgumentException('Cannot have more than 90 degrees of latitude');
        }

        self::validateMinutesAndSeconds($minutes, $seconds);
    }

    private static function validateLongitude(int $degrees, int $minutes, float $seconds): void
    {
        if ($degrees === 180 && ($minutes !== 0 || $seconds !== 0.0)) {
            throw new InvalidArgumentException('Cannot have more than 180 degrees of longitude');
        } elseif ($degrees > 180) {
            throw new InvalidArgumentException('Cannot have more than 180 degrees of longitude');
        }

        self::validateMinutesAndSeconds($minutes, $seconds);
    }

    private static function validateMinutesAndSeconds(int $minutes, float $seconds)
    {
        if ($minutes > 60) {
            throw new InvalidArgumentException('Cannot have more than 60 minutes');
        }

        if ($seconds > 60) {
            throw new InvalidArgumentException('Cannot have more than 60 seconds');
        }
    }

    private static function convertToDecimal(int $degrees, int $minutes, float $seconds, int $multiplier): float
    {
        return ($degrees + ($minutes / 60) + ($seconds / 3600)) * $multiplier;
    }
}
