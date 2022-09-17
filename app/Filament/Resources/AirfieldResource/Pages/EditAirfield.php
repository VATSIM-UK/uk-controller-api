<?php

namespace App\Filament\Resources\AirfieldResource\Pages;

use App\Filament\Resources\AirfieldResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAirfield extends EditRecord
{
    protected static string $resource = AirfieldResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
