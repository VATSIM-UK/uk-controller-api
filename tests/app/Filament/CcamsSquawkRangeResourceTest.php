<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\AccessCheckingHelpers\ChecksCreateFilamentAccess;
use App\Filament\AccessCheckingHelpers\ChecksEditFilamentAccess;
use App\Filament\AccessCheckingHelpers\ChecksListingFilamentAccess;
use App\Filament\Resources\CcamsSquawkRangeResource;
use App\Models\Squawk\Ccams\CcamsSquawkRange;
use Illuminate\Database\Eloquent\Model;

class CcamsSquawkRangeResourceTest extends BaseFilamentTestCase
{
    use ChecksCreateFilamentAccess;
    use ChecksEditFilamentAccess;
    use ChecksListingFilamentAccess;
    use ChecksFilamentActionVisibility;

    protected function getCreateText(): string
    {
        return 'Create ccams squawk range';
    }

    protected function getEditRecord(): Model
    {
        return CcamsSquawkRange::findOrFail(1);
    }

    protected function getEditText(): string
    {
        return 'Edit ccams squawk range';
    }

    protected function getIndexText(): array
    {
        return ['Ccams Squawk Ranges', '0303'];
    }

    protected function getViewText(): string
    {
        return '';
    }

    protected function getViewRecord(): Model
    {
        return CcamsSquawkRange::findOrFail(1);
    }

    protected function resourceClass(): string
    {
        return CcamsSquawkRangeResource::class;
    }

    protected function resourceListingClass(): string
    {
        return CcamsSquawkRangeResource\Pages\ListCcamsSquawkRanges::class;
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
