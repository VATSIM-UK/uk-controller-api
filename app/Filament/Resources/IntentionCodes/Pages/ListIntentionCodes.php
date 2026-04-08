<?php

namespace App\Filament\Resources\IntentionCodes\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\IntentionCodes\IntentionCodeResource;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Pages\Actions;
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
