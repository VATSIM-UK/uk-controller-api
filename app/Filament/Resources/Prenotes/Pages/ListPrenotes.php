<?php

namespace App\Filament\Resources\Prenotes\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\Prenotes\PrenoteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrenotes extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = PrenoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
