<?php

namespace App\Filament\Resources\PrenoteResource\Pages;

use App\Filament\Resources\PrenoteResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPrenote extends ViewRecord
{
    protected static string $resource = PrenoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
