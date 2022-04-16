<?php

namespace App\Services;

use App\Models\IntentionCode\IntentionCode;

class IntentionCodeService
{
    public function getIntentionCodesDependency(): array
    {
        return IntentionCode::orderBy('priority')
            ->get()
            ->toArray();
    }
}
