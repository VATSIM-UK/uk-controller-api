<?php

namespace App\Filament\Resources\Navaids\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Navaids\NavaidResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNavaid extends EditRecord
{
    protected static string $resource = NavaidResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
