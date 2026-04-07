<?php

namespace App\Filament\Resources\FirExitPointResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\FirExitPointResource;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ManageFirExitPoints extends ListRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = FirExitPointResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
