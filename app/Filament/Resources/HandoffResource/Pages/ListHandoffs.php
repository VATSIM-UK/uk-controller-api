<?php

namespace App\Filament\Resources\HandoffResource\Pages;

use App\Filament\Resources\HandoffResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHandoffs extends ListRecords
{
    protected static string $resource = HandoffResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
