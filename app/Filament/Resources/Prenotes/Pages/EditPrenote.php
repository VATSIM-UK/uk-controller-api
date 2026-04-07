<?php

namespace App\Filament\Resources\Prenotes\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Prenotes\PrenoteResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrenote extends EditRecord
{
    protected static string $resource = PrenoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
