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
    use ChecksDefaultFilamentActionVisibility;

    public function testItCreatesASquawkRange()
    {
        Livewire::test(ManageCcamsSquawkRange::class)
            ->callAction(
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
            ->callAction(
                'create',
                [
                    'first' => '123a',
                    'last' => '2345',
                ]
            )
            ->assertHasActionErrors(['first']);
    }

    public function testItDoesntCreateARangeIfLastInvalid()
    {
        Livewire::test(ManageCcamsSquawkRange::class)
            ->callAction(
                'create',
                [
                    'first' => '1234',
                    'last' => '234b',
                ]
            )
            ->assertHasActionErrors(['last']);
    }

    public function testItEditsASquawkRange()
    {
        Livewire::test(ManageCcamsSquawkRange::class)
            ->callTableAction(
                'edit',
                CcamsSquawkRange::findOrFail(1),
                [
                    'first' => '3456',
                    'last' => '4567',
                ]
            )
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'ccams_squawk_ranges',
            [
                'id' => 1,
                'first' => '3456',
                'last' => '4567',
            ]
        );
    }

    public function testItDoesntEditARangeIfFirstInvalid()
    {
        Livewire::test(ManageCcamsSquawkRange::class)
            ->callTableAction(
                'edit',
                CcamsSquawkRange::findOrFail(1),
                [
                    'first' => '234a',
                    'last' => '4567',
                ]
            )
            ->assertHasTableActionErrors(['first']);
    }

    public function testItDoesntEditARangeIfLastInvalid()
    {
        Livewire::test(ManageCcamsSquawkRange::class)
            ->callTableAction(
                'edit',
                CcamsSquawkRange::findOrFail(1),
                [
                    'first' => '2345',
                    'last' => '456a',
                ]
            )
            ->assertHasTableActionErrors(['last']);
    }

    protected function getCreateText(): string
    {
        return 'Create ccams squawk range';
    }

    protected function getIndexText(): array
    {
        return ['Ccams Squawk Ranges', '0303'];
    }

    protected static function resourceClass(): string
    {
        return CcamsSquawkRangeResource::class;
    }

    protected static function resourceListingClass(): string
    {
        return ManageCcamsSquawkRange::class;
    }

    protected static function resourceRecordClass(): string
    {
        return CcamsSquawkRange::class;
    }

    protected static function resourceId(): int|string
    {
        return 1;
    }

    protected static function writeResourceTableActions(): array
    {
        return ['edit'];
    }

    protected static function writeResourcePageActions(): array
    {
        return ['create'];
    }
}
