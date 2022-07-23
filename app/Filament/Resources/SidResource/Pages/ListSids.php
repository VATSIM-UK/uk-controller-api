<?php

namespace App\Filament\Resources\SidResource\Pages;

use App\Filament\Resources\SidResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSids extends ListRecords
{
    protected static string $resource = SidResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
