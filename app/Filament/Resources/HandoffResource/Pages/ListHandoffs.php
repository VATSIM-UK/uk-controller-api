<?php

namespace App\Filament\Resources\HandoffResource\Pages;

use App\Filament\Resources\HandoffResource;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHandoffs extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = HandoffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
