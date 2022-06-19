<?php

namespace App\Filament\Resources\StandResource\Pages;

use App\Filament\Resources\StandResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStands extends ListRecords
{
    protected static string $resource = StandResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
