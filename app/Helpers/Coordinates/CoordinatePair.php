<?php

namespace App\Helpers\Coordinates;

class CoordinatePair
{
    /**
     * @var Coordinate
     */
    private $latitude;
    /**
     * @var Coordinate
     */
    private $longitude;

    private function __construct(Coordinate $latitude, Coordinate $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public static function fromSectorfileCoordinates(string $latitude, string $longitude)
    {
        return new self(Coordinate::fromSectorfileFormat($latitude), Coordinate::fromSectorfileFormat($longitude));
    }

    public static function fromDecimal(float $latitude, float $longitude)
    {
        return new self(Coordinate::latitudeFromDecimal($latitude), Coordinate::longitudeFromDecimal($longitude));
    }

    public function getSectorfileFormat(): string
    {
        return sprintf(
            '%s %s',
            $this->getLatitude()->getSectorFileFormat(),
            $this->getLongitude()->getSectorFileFormat()
        );
    }

    /**
     * Calculate the great circle distance between two coordinate pairs
     * in nautical miles.
     *
     * @param CoordinatePair $compare
     * @return float
     */
    public function getDistance(CoordinatePair $compare): float
    {
        $latitudeFromRadians = $this->getLatitude()->convertToRadians();
        $latitudeToRadians = $compare->getLatitude()->convertToRadians();
        $latitudeDelta = $latitudeToRadians - $latitudeFromRadians;
        $longitudeDelta =  $compare->getLongitude()->convertToRadians() - $this->getLongitude()->convertToRadians();

        return (2 * asin(sqrt(pow(sin($latitudeDelta / 2), 2) +
                    cos($latitudeFromRadians) * cos($latitudeToRadians) * pow(sin($longitudeDelta / 2),
                        2)))) * (6371000 * 0.0005399568);
    }

    public function getAbsoluteDistance(CoordinatePair $compare)
    {
        return abs($this->getDistance($compare));
    }

    /**
     * @return Coordinate
     */
    public function getLatitude(): Coordinate
    {
        return $this->latitude;
    }

    /**
     * @return Coordinate
     */
    public function getLongitude(): Coordinate
    {
        return $this->longitude;
    }
}
