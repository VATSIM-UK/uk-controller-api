<?php

namespace App\Filament\Resources\SmrAreas\Pages;

use Filament\Actions\EditAction;
use App\Filament\Resources\SmrAreas\SmrAreaResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSmrArea extends ViewRecord
{
    protected static string $resource = SmrAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
