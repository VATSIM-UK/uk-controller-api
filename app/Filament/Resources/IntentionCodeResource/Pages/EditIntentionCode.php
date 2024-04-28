<?php

namespace App\Filament\Resources\IntentionCodeResource\Pages;

use App\Filament\Resources\IntentionCodeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditIntentionCode extends EditRecord
{
    use MutatesIntentionCodes;
    use FillsIntentionCodeForms;
    use SavesIntentionCodes;

    protected static string $resource = IntentionCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->mutateIntentionCode($data);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data) {
            return $this->saveIntentionCode($data, $record);
        });
    }
}
