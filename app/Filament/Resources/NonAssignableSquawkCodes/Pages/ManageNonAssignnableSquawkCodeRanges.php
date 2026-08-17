<?php

namespace App\Filament\Resources\NonAssignableSquawkCodes\Pages;

use App\Filament\Resources\NonAssignableSquawkCodes\NonAssignableSquawkCodeResource;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageNonAssignnableSquawkCodeRanges extends ManageRecords
{
    use LimitsTableRecordListingOptions;

    protected static string $resource = NonAssignableSquawkCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
