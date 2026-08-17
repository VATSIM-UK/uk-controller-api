<?php

namespace App\Filament\Resources\IntentionCodes\Pages;

use App\Filament\Resources\IntentionCodes\IntentionCodeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewIntentionCode extends ViewRecord
{
    use FillsIntentionCodeForms;

    protected static string $resource = IntentionCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
