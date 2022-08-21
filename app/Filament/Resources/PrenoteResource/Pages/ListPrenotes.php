<?php

namespace App\Filament\Resources\PrenoteResource\Pages;

use App\Filament\Resources\PrenoteResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrenotes extends ListRecords
{
    protected static string $resource = PrenoteResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
