<?php

namespace App\Helpers\Sectorfile;

class Coordinate
{
    private string $latitude;
    private string $longitude;

    public function __construct(string $latitude, string $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function getLatitude(): string
    {
        return $this->latitude;
    }

    public function getLongitude(): string
    {
        return $this->longitude;
    }

    public function __toString(): string
    {
        return sprintf('%s %s', $this->latitude, $this->longitude);
    }
}
