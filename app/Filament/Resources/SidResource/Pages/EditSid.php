<?php

namespace App\Filament\Resources\SidResource\Pages;

use App\Filament\Resources\SidResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSid extends EditRecord
{
    protected static string $resource = SidResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
