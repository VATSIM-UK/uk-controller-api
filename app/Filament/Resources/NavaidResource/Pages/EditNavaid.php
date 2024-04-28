<?php

namespace App\Filament\Resources\NavaidResource\Pages;

use App\Filament\Resources\NavaidResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNavaid extends EditRecord
{
    protected static string $resource = NavaidResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
