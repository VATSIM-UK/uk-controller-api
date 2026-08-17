<?php

namespace App\Filament\Resources\UnitDiscreteSquawkRanges\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\UnitDiscreteSquawkRanges\Traits\MutatesRuleData;
use App\Filament\Resources\UnitDiscreteSquawkRanges\UnitDiscreteSquawkRangeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageUnitDiscreteSquawkRanges extends ManageRecords
{
    use LimitsTableRecordListingOptions;
    use MutatesRuleData;

    protected static string $resource = UnitDiscreteSquawkRangeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateDataUsing(fn (array $data) => self::mutateFormData()($data)),
        ];
    }
}
