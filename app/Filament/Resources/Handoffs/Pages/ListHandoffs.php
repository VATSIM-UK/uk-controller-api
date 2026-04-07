<?php

namespace App\Filament\Resources\Handoffs\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Handoffs\HandoffResource;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHandoffs extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = HandoffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
