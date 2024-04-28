<?php

namespace App\Filament\Resources\TerminalResource\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\TerminalResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTerminals extends ListRecords
{
    use LimitsTableRecordListingOptions;
    protected static string $resource = TerminalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
