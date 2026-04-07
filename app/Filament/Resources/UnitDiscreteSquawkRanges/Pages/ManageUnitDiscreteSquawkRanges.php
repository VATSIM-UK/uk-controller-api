<?php

namespace App\Filament\Resources\UnitDiscreteSquawkRanges\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\UnitDiscreteSquawkRanges\UnitDiscreteSquawkRangeResource;
use App\Filament\Resources\UnitDiscreteSquawkRanges\Traits\MutatesRuleData;
use Filament\Resources\Pages\ManageRecords;
use Filament\Actions\CreateAction;

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
