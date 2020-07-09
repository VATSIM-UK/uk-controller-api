<?php

namespace App\Helpers\Coordinates;

use InvalidArgumentException;

final class Coordinate
{
    const TYPE_LATITUDE = 0;
    const TYPE_LONGITUDE = 1;
    const MULTIPLIER_POSITIVE = 1;
    const MULTIPLIER_NEGATIVE = -1;
    const SECTORFILE_FORMAT_REGEX = "/^([N,S,E,W])(\\d{3})\\.(\\d{2})\\.(\\d{2})\\.(\\d{3})$/";

    /**
     * @var float
     */
    private $decimal;
    /**
     * @var int
     */
    private $type;

    private function __construct(int $type, float $decimal)
    {
        $this->decimal = $decimal;
        $this->type = $type;
    }

    public static function fromSectorfileFormat(string $coordinate): Coordinate
    {
        $matches = [];
        if (preg_match(self::SECTORFILE_FORMAT_REGEX, $coordinate, $matches) !== 1) {
            throw new InvalidArgumentException('Invalid sectorfile coordinate format ' . $coordinate);
        }

        // Capture the type
        $type = $matches[1] === 'N' || $matches[1] === 'S' ? self::TYPE_LATITUDE : self::TYPE_LONGITUDE;

        // Covert and validate DMS
        $degrees = (int) ltrim($matches[2], '0');
        $minutes = (int) ltrim($matches[3], '0');
        $seconds = (float) ltrim(sprintf('%s.%s', $matches[4], $matches[5]), '0');
        static::validateDegrees($type, $degrees, $minutes, $seconds);

        // Work out the degrees
        $decimal = static::convertToDecimal(
            $degrees,
            $minutes,
            $seconds,
            $matches[1] === 'S' || $matches[1] === 'W' ? static::MULTIPLIER_NEGATIVE : static::MULTIPLIER_POSITIVE
        );

        // Return new self
        return new self($type, $decimal);
    }

    public static function latitudeFromDecimal(float $decimal): Coordinate
    {
        if (abs($decimal) > 90) {
            throw new InvalidArgumentException('Cannot have more than 90 degrees of latitude, got ' . $decimal);
        }

        return new self(self::TYPE_LATITUDE, $decimal);
    }

    public static function longitudeFromDecimal(float $decimal): Coordinate
    {
        if (abs($decimal) > 180) {
            throw new InvalidArgumentException('Cannot have more than 180 degrees of longitude, got ' . $decimal);
        }

        return new self(self::TYPE_LONGITUDE, $decimal);
    }

    public function getSectorFileFormat(): string
    {
        $direction = null;
        if ($this->type === self::TYPE_LATITUDE) {
            $direction = $this->decimal < 0 ? 'S' : 'N';
        } elseif ($this->type === self::TYPE_LONGITUDE) {
            $direction = $this->decimal < 0  ? 'W' : 'E';
        }

        return sprintf(
            '%s%s.%s.%s',
            $direction,
            str_pad(abs(self::getDegrees($this->decimal)), 3, '0', STR_PAD_LEFT),
            str_pad(abs(self::getMinutes($this->decimal)), 2, '0', STR_PAD_LEFT),
            str_pad(number_format(abs(self::getSeconds($this->decimal)), 3), 6, '0', STR_PAD_LEFT),
        );
    }

    private static function getDegrees(float $decimal): int
    {
        return (int) $decimal;
    }

    private static function getMinutes(float $decimal): int
    {
        return (int)(($decimal - static::getDegrees($decimal)) * 60);
    }

    private static function getSeconds(float $decimal): float
    {
        return ($decimal - static::getDegrees($decimal) - static::getMinutes($decimal) / 60) * 3600;
    }

    private static function validateDegrees(int $type, int $degrees, int $minutes, float $seconds): void
    {
        if ($type === self::TYPE_LATITUDE) {
            if ($degrees === 90 && ($minutes !== 0 || $seconds !== 0.0)) {
                throw new InvalidArgumentException('Cannot have more than 90 degrees of latitude');
            } else if ($degrees > 90) {
                throw new InvalidArgumentException('Cannot have more than 90 degrees of latitude');
            }
        } elseif ($type === self::TYPE_LONGITUDE) {
            if ($degrees === 180 && ($minutes !== 0 || $seconds !== 0.0)) {
                throw new InvalidArgumentException('Cannot have more than 180 degrees of longitude');
            } else if ($degrees > 180) {
                throw new InvalidArgumentException('Cannot have more than 180 degrees of longitude');
            }
        }

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

    public function convertToRadians(): float
    {
        return deg2rad($this->decimal);
    }

    /**
     * @return float
     */
    public function getDecimal(): float
    {
        return $this->decimal;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }
}
