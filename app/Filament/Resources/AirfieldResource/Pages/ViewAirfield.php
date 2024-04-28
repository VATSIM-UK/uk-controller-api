<?php

namespace App\Filament\Resources\AirfieldResource\Pages;

use App\Filament\Resources\AirfieldResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAirfield extends ViewRecord
{
    protected static string $resource = AirfieldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
