<?php

namespace App\Filament\Resources\IntentionCodeResource\Pages;

use App\Filament\Resources\IntentionCodeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewIntentionCode extends ViewRecord
{
    use FillsIntentionCodeForms;

    protected static string $resource = IntentionCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
