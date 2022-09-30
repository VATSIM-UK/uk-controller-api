<?php

namespace App\Filament\Resources\AirfieldResource\Pages;

use App\Filament\Resources\AirfieldResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAirfields extends ListRecords
{
    protected static string $resource = AirfieldResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
