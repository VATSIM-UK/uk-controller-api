<?php

namespace App\Filament\Resources\IntentionCodeResource\Pages;

use App\Models\IntentionCode\IntentionCode;
use App\Services\IntentionCode\IntentionCodeService;

trait SavesIntentionCodes
{
    private function saveIntentionCode(array $data, ? IntentionCode $existing = null): IntentionCode
    {
        $previousPriority = $existing?->priority;
        $code = $existing
            ? $existing->fill($data)
            : new IntentionCode($data);

        IntentionCodeService::saveIntentionCode($code, $previousPriority);

        return $code;
    }
}
