<?php

namespace App\Filament\Resources\IntentionCodeResource\Pages;

use App\Filament\Resources\IntentionCodeResource;
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
            Actions\CreateAction::make(),
        ];
    }
}
