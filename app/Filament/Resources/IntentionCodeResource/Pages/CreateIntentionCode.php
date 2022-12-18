<?php

namespace App\Filament\Resources\IntentionCodeResource\Pages;

use App\Filament\Resources\IntentionCodeResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateIntentionCode extends CreateRecord
{
    use MutatesIntentionCodes;
    use SavesIntentionCodes;

    protected static string $resource = IntentionCodeResource::class;

    public function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->mutateIntentionCode($data);
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            return $this->saveIntentionCode($data);
        });
    }
}
