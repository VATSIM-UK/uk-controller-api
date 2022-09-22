<?php

namespace App\Filament\Resources\RunwayResource\Pages;

use App\Filament\Resources\RunwayResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRunways extends ListRecords
{
    protected static string $resource = RunwayResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
