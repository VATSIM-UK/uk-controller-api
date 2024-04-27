<?php

namespace App\Filament\AccessCheckingHelpers;

trait HasResourceClass
{
    /**
     * Get the resource this test is for.
     */
    protected static abstract function resourceClass(): string;
}
