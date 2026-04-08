<?php

namespace App\Filament\Resources\Notifications\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Notifications\NotificationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNotification extends EditRecord
{
    protected static string $resource = NotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
