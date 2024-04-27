<?php

namespace App\Filament\Resources\AircraftResource\Pages;

use App\Events\Aircraft\AircraftDataUpdatedEvent;
use App\Filament\Resources\AircraftResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAircraft extends EditRecord
{
    protected static string $resource = AircraftResource::class;

    protected function afterSave(): void
    {
        event(new AircraftDataUpdatedEvent);
    }

    protected function afterDelete(): void
    {
        event(new AircraftDataUpdatedEvent);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(function () {
                    event(new AircraftDataUpdatedEvent);
                }),
        ];
    }
}
