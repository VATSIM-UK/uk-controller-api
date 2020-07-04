<?php

namespace App\Generator\Squawk\General;

interface GeneralSquawkGeneratorInterface
{
    public function generate(string $origin, string $destination): ?string;
}
