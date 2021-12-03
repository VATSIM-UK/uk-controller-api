<?php

namespace App\Services\Metar;

use Illuminate\Support\Collection;

class MetarTokeniser
{
    public function tokenise(string $metar): Collection
    {
        return collect(explode(' ', $metar));
    }
}
