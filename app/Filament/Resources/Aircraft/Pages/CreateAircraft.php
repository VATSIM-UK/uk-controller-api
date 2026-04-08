<?php

namespace App\Filament\Resources\Aircraft\Pages;

use App\Events\Aircraft\AircraftDataUpdatedEvent;
use App\Filament\Resources\Aircraft\AircraftResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAircraft extends CreateRecord
{
    protected static string $resource = AircraftResource::class;

    protected function afterCreate(): void
    {
        event(new AircraftDataUpdatedEvent);
    }
}
