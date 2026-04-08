<?php

namespace App\Filament\Resources\Airfields\Pages;

use Filament\Actions\EditAction;
use App\Filament\Resources\Airfields\AirfieldResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAirfield extends ViewRecord
{
    protected static string $resource = AirfieldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
