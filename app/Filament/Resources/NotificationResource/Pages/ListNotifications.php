<?php

namespace App\Filament\Resources\NotificationResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\NotificationResource;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNotifications extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = NotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
