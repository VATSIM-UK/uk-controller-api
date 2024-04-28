<?php

namespace App\Filament\Resources\AirlineResource\Pages;

use App\Events\Airline\AirlinesUpdatedEvent;
use App\Filament\Resources\AirlineResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAirline extends EditRecord
{
    public static string $resource = AirlineResource::class;

    protected function afterSave(): void
    {
        event(new AirlinesUpdatedEvent);
    }

    protected function afterDelete(): void
    {
        event(new AirlinesUpdatedEvent);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->after(function () {
                    event(new AirlinesUpdatedEvent);
                }),
        ];
    }
}
