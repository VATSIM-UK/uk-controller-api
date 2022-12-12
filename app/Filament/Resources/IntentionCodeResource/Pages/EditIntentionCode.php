<?php

namespace App\Filament\Resources\IntentionCodeResource\Pages;

use App\Filament\Resources\IntentionCodeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIntentionCode extends EditRecord
{
    use MutatesIntentionCodes;
    use FillsIntentionCodeForms;

    protected static string $resource = IntentionCodeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->mutateIntentionCode($data);
    }
}
