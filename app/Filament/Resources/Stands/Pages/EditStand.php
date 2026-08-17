<?php

namespace App\Filament\Resources\Stands\Pages;

use App\Filament\Resources\Stands\StandResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStand extends EditRecord
{
    protected static string $resource = StandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
