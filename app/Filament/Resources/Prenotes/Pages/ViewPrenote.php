<?php

namespace App\Filament\Resources\Prenotes\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Prenotes\PrenoteResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPrenote extends ViewRecord
{
    protected static string $resource = PrenoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
