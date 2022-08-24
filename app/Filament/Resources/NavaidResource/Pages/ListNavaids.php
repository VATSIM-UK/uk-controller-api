<?php

namespace App\Filament\Resources\NavaidResource\Pages;

use App\Filament\Resources\NavaidResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNavaids extends ListRecords
{
    protected static string $resource = NavaidResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
