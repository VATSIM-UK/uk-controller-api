<?php

namespace App\Filament\Resources\Airfields\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Airfields\AirfieldResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditAirfield extends EditRecord
{
    protected static string $resource = AirfieldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
        ];
    }
}
