<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\AccessCheckingHelpers\ChecksManageRecordsFilamentAccess;
use App\Filament\Resources\CcamsSquawkRangeResource;
use App\Filament\Resources\CcamsSquawkRangeResource\Pages\ListCcamsSquawkRanges;
use App\Models\Squawk\Ccams\CcamsSquawkRange;
use Illuminate\Database\Eloquent\Model;

class CcamsSquawkRangeResourceTest extends BaseFilamentTestCase
{
    use ChecksManageRecordsFilamentAccess;
    use ChecksFilamentActionVisibility;

    protected function getCreateText(): string
    {
        return 'Create ccams squawk range';
    }

    protected function getIndexText(): array
    {
        return ['Ccams Squawk Ranges', '0303'];
    }

    protected function resourceClass(): string
    {
        return CcamsSquawkRangeResource::class;
    }

    protected function resourceListingClass(): string
    {
        return ListCcamsSquawkRanges::class;
    }

    protected function resourceRecordClass(): string
    {
        return CcamsSquawkRange::class;
    }

    protected function resourceId(): int|string
    {
        return 1;
    }

    protected function writeResourceTableActions(): array
    {
        return ['edit'];
    }

    protected function writeResourcePageActions(): array
    {
        return ['create'];
    }
}
