<?php

namespace App\Filament\Resources\IntentionCodeResource\Pages;

use App\Filament\Resources\IntentionCodeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIntentionCode extends CreateRecord
{
    use MutatesIntentionCodes;

    protected static string $resource = IntentionCodeResource::class;

    public function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->mutateIntentionCode($data);
    }
}
