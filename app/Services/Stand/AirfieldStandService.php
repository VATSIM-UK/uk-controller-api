<?php

namespace App\Services\Stand;

use App\Models\Airfield\Airfield;
use App\Models\Stand\Stand;
use Illuminate\Support\Collection;

class AirfieldStandService
{
    private Collection $allStandsByAirfield;

    public function __construct()
    {
        $this->allStandsByAirfield = collect();
    }

    /**
     * @return Collection|Stand[]
     */
    public function getAllStandsByAirfield(): Collection
    {
        if ($this->allStandsByAirfield->isEmpty()) {
            $this->allStandsByAirfield = Airfield::with('stands')->whereHas('stands')->get()->toBase();
        }

        return $this->allStandsByAirfield;
    }
}
