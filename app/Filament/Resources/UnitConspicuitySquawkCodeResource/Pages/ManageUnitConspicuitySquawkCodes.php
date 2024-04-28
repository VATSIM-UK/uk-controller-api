<?php

namespace App\Filament\Resources\UnitConspicuitySquawkCodeResource\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\UnitConspicuitySquawkCodeResource;
use App\Filament\Resources\UnitDiscreteSquawkRangeResource\Traits\MutatesRuleData;
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
                ->mutateFormDataUsing(fn (array $data) => self::mutateFormData()($data)),
        ];
    }
}
