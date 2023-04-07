<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Support\Str;

trait UsesCallsignSlugs
{
    private function getCallsignSlugs(NetworkAircraft $aircraft): array
    {
        $slug = $this->airlineService->getCallsignSlugForAircraft($aircraft);
        $slugs = [];
        for ($i = 0; $i < Str::length($slug); $i++) {
            $slugs[] = Str::substr($slug, 0, $i + 1);
        }

        return $slugs;
    }
}
