<?php

namespace App\Filament\Resources\Terminals\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\Terminals\TerminalResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTerminals extends ListRecords
{
    use LimitsTableRecordListingOptions;
    protected static string $resource = TerminalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
