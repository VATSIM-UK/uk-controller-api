<?php

namespace App\Allocator\Squawk;

class SquawkAssignmentCategories
{
    const LOCAL = 'LOCAL';
    const GENERAL = 'GENERAL';

    const TYPES = [
        self::LOCAL,
        self::GENERAL
    ];

    private function __construct()
    {
        // Private constructor as class is only for constants.
    }
}
