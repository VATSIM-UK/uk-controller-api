<?php

namespace App\Filament\Resources\UnitDiscreteSquawkRangeResource\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\UnitDiscreteSquawkRangeResource;
use App\Filament\Resources\UnitDiscreteSquawkRangeResource\Traits\MutatesRuleData;
use Filament\Resources\Pages\ManageRecords;

class ManageUnitDiscreteSquawkRanges extends ManageRecords
{
    use LimitsTableRecordListingOptions;
    use MutatesRuleData;

    protected static string $resource = UnitDiscreteSquawkRangeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return self::mutateFormData()($data);
    }
}
