<?php

namespace App\Helpers\Vatsim;

interface ControllerPositionInterface
{
    public function getCallsign(): string;

    public function getFrequency(): float;
}
