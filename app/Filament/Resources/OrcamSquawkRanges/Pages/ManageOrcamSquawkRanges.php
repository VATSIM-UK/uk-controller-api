<?php

namespace App\Filament\Resources\OrcamSquawkRanges\Pages;

use App\Filament\Resources\OrcamSquawkRanges\OrcamSquawkRangeResource;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageOrcamSquawkRanges extends ManageRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = OrcamSquawkRangeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
