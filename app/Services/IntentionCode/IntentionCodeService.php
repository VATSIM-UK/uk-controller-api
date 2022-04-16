<?php

namespace App\Services\IntentionCode;

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
