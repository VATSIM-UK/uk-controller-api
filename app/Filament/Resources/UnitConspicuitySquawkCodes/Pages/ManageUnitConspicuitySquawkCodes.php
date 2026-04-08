<?php

namespace App\Filament\Resources\UnitConspicuitySquawkCodes\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\UnitConspicuitySquawkCodes\UnitConspicuitySquawkCodeResource;
use App\Filament\Resources\UnitDiscreteSquawkRanges\Traits\MutatesRuleData;
use Filament\Resources\Pages\ManageRecords;
use Filament\Actions\CreateAction;

class ManageUnitConspicuitySquawkCodes extends ManageRecords
{
    use LimitsTableRecordListingOptions;
    use MutatesRuleData;

    protected static string $resource = UnitConspicuitySquawkCodeResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateDataUsing(fn (array $data) => self::mutateFormData()($data)),
        ];
    }
}
