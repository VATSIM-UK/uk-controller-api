<?php

namespace App\Filament\Resources\RunwayResource\Pages;

use App\Filament\Resources\RunwayResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRunway extends EditRecord
{
    protected static string $resource = RunwayResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
