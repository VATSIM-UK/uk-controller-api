<?php

namespace App\Filament\Resources\Sids\Pages;

use App\Filament\Resources\Sids\SidResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSid extends EditRecord
{
    protected static string $resource = SidResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
