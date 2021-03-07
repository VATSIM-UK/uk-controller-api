<?php

namespace App\Services;

use App\Models\Stand\Stand;
use App\Models\Stand\StandType;
use App\Models\Airfield\Airfield;
use Illuminate\Database\Eloquent\Collection;

class StandAdminService 
{
    /**
     * Return a list of stand types.
     *
     * @return Collection
     */
    public static function standTypes(): Collection
    {
        return StandType::all();
    }

    /**
     * Get stands by airfield. Query optimised to be used in
     * index style view.
     *
     * @param Airfield $airfield
     * @return Collection
     */
    public function getStandsByAirfield(Airfield $airfield) : Collection
    {
        return Stand::with(['type', 'terminal', 'wakeCategory'])->withCount(['airlines'])->where('airfield_id', $airfield->id)->get();
    }
}
