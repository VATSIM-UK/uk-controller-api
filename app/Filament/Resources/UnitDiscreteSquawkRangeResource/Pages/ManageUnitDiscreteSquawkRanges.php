<?php

namespace App\Filament\Resources\UnitDiscreteSquawkRangeResource\Pages;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\UnitDiscreteSquawkRangeResource;
use App\Filament\Resources\UnitDiscreteSquawkRangeResource\Traits\MutatesRuleData;
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
                ->mutateFormDataUsing(fn (array $data) => self::mutateFormData()($data)),
        ];
    }
}
