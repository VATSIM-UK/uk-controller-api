<?php

namespace App\Filament\Resources\Terminals\Pages;

use App\Filament\Resources\Terminals\TerminalResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTerminal extends EditRecord
{
    protected static string $resource = TerminalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
