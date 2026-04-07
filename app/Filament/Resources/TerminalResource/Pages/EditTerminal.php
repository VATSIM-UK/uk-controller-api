<?php

namespace App\Filament\Resources\TerminalResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\TerminalResource;
use Filament\Pages\Actions;
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
