<?php

namespace App\Filament\Resources\RunwayResource\Pages;

use App\Models\Runway\Runway;
use App\Services\RunwayService;
use Illuminate\Support\Facades\DB;

trait SetsRunwayInverses
{
    public function setInverse(Runway $runway): void
    {
        $inverseRunway = Runway::where('airfield_id', $runway->airfield_id)
            ->where('identifier', RunwayService::inverseRunwayIdentifier($runway->identifier))
            ->first();

        if (!$inverseRunway) {
            return;
        }

        DB::transaction(function () use ($runway, $inverseRunway) {
            $runway->inverses()->sync($inverseRunway->id);
            $inverseRunway->inverses()->sync($runway->id);
        });
    }
}
