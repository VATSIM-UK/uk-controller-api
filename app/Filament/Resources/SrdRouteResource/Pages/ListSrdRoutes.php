<?php

namespace App\Filament\Resources\SrdRouteResource\Pages;

use App\Filament\Resources\SrdRouteResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSrdRoutes extends ListRecords
{
    protected static string $resource = SrdRouteResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
