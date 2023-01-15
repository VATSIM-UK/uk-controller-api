<?php

namespace App\Services\IntentionCode;

use App\Models\IntentionCode\FirExitPoint;

class FirExitPointService
{
    public function getFirExitDependency(): array
    {
        return FirExitPoint::all()
            ->makeHidden(['created_at', 'updated_at'])
            ->toArray();
    }
}
