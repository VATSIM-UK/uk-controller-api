<?php

namespace App\Filament\Resources\PrenoteResource\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\PrenoteResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrenotes extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = PrenoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
