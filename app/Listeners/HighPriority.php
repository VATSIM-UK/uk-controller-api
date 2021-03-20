<?php

namespace App\Listeners;

trait HighPriority
{
    public function viaQueue(): string
    {
        return 'high';
    }
}
