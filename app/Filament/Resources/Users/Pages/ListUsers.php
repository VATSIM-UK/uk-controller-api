<?php

namespace App\Filament\Resources\Users\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\Users\UserResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
