<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\AccessCheckingHelpers\ChecksManageRecordsFilamentAccess;
use App\Filament\Resources\CcamsSquawkRangeResource;
use App\Filament\Resources\CcamsSquawkRangeResource\Pages\ManageCcamsSquawkRange;
use App\Models\Squawk\Ccams\CcamsSquawkRange;
use Livewire\Livewire;

class CcamsSquawkRangeResourceTest extends BaseFilamentTestCase
{
    use ChecksManageRecordsFilamentAccess;
    use ChecksFilamentActionVisibility;

    public function testItCreatesASquawkRange()
    {
        Livewire::test(ManageCcamsSquawkRange::class)
            ->callPageAction(
                'create',
                [
                    'first' => '1234',
                    'last' => '2345',
                ]
            )
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'ccams_squawk_ranges',
            [
                'first' => '1234',
                'last' => '2345',
            ]
        );
    }

    public function testItDoesntCreateARangeIfFirstInvalid()
    {
        Livewire::test(ManageCcamsSquawkRange::class)
            ->callPageAction(
                'create',
                [
                    'first' => '123a',
                    'last' => '2345',
                ]
            )
            ->assertHasPageActionErrors(['first']);
    }

    public function testItDoesntCreateARangeIfLastInvalid()
    {
        Livewire::test(ManageCcamsSquawkRange::class)
            ->callPageAction(
                'create',
                [
                    'first' => '1234',
                    'last' => '234b',
                ]
            )
            ->assertHasPageActionErrors(['last']);
    }

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
        return ManageCcamsSquawkRange::class;
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
