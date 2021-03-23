<?php

namespace App\Events;

use Illuminate\Support\Collection;

class MetarsUpdatedEvent
{
    private Collection $metars;

    public function __construct(Collection $metars)
    {
        $this->metars = $metars;
    }

    public function getMetars(): Collection
    {
        return $this->metars;
    }
}
