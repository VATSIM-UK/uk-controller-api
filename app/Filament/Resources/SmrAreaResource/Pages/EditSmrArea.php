<?php

namespace App\Filament\Resources\SmrAreaResource\Pages;

use App\Filament\Resources\SmrAreaResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSmrArea extends EditRecord
{
    protected static string $resource = SmrAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
