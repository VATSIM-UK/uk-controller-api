<?php

namespace App\Filament\Resources\SmrAreaResource\Pages;

use App\Filament\Resources\SmrAreaResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSmrArea extends ViewRecord
{
    protected static string $resource = SmrAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
