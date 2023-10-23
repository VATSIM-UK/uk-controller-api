<?php

namespace App\Filament\Resources\AirfieldResource\Pages;

use App\Filament\Resources\AirfieldResource;
use Filament\Pages\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditAirfield extends EditRecord
{
    protected static string $resource = AirfieldResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make()
        ];
    }
}
