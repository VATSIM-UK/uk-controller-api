<?php


namespace App\Helpers\Vatsim;


class ParsedControllerPosition
{
    private string $facility;
    private string $unitType;
    private float $frequency;

    public function __construct(string $facility, string $unitType, float $frequency)
    {
        $this->facility = $facility;
        $this->unitType = $unitType;
        $this->frequency = $frequency;
    }
    
    public function getFacility(): string
    {
        return $this->facility;
    }
    
    public function getUnitType(): string
    {
        return $this->unitType;
    }

    public function getFrequency(): float
    {
        return $this->frequency;
    }
}
