<?php

namespace App\Filament\Resources\IntentionCodes\Pages;

use App\Filament\Resources\IntentionCodes\IntentionCodeResource;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIntentionCodes extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = IntentionCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
