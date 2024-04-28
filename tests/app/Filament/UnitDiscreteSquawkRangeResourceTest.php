<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\AccessCheckingHelpers\ChecksManageRecordsFilamentAccess;
use App\Filament\Resources\UnitDiscreteSquawkRangeResource;
use App\Filament\Resources\UnitDiscreteSquawkRangeResource\Pages\ManageUnitDiscreteSquawkRanges;
use App\Models\Squawk\UnitDiscrete\UnitDiscreteSquawkRange;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Livewire;

class UnitDiscreteSquawkRangeResourceTest extends BaseFilamentTestCase
{
    use ChecksManageRecordsFilamentAccess;
    use ChecksDefaultFilamentActionVisibility;

    public function testItCreatesASquawkRange()
    {
        Livewire::test(ManageUnitDiscreteSquawkRanges::class)
            ->callAction(
                'create',
                [
                    'first' => '1234',
                    'last' => '2345',
                    'unit' => 'EGKK',
                ]
            )
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'unit_discrete_squawk_ranges',
            [
                'first' => '1234',
                'last' => '2345',
                'unit' => 'EGKK',
            ]
        );
    }

    public function testItCreatesASquawkRangeWithRules()
    {
        Livewire::test(ManageUnitDiscreteSquawkRanges::class)
            ->callAction(
                'create',
                [
                    'first' => '1234',
                    'last' => '2345',
                    'unit' => 'EGKK',
                    'flight_rules' => 'VFR',
                    'service' => 'BASIC',
                    'unit_type' => 'APP',
                ]
            )
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'unit_discrete_squawk_ranges',
            [
                'first' => '1234',
                'last' => '2345',
                'unit' => 'EGKK',
            ]
        );
        $this->assertEquals(
            [
                ['type' => 'SERVICE', 'rule' => 'BASIC'],
                ['type' => 'FLIGHT_RULES', 'rule' => 'VFR'],
                ['type' => 'UNIT_TYPE', 'rule' => 'APP'],
            ],
            UnitDiscreteSquawkRange::latest()->first()->rules
        );
    }

    public function testItDoesntCreateARangeIfFirstInvalid()
    {
        Livewire::test(ManageUnitDiscreteSquawkRanges::class)
            ->callAction(
                'create',
                [
                    'first' => '123a',
                    'last' => '2345',
                    'unit' => 'EGKK',
                ]
            )
            ->assertHasActionErrors(['first']);
    }

    public function testItDoesntCreateARangeIfLastInvalid()
    {
        Livewire::test(ManageUnitDiscreteSquawkRanges::class)
            ->callAction(
                'create',
                [
                    'first' => '1234',
                    'last' => '234b',
                    'unit' => 'EGKK',
                ]
            )
            ->assertHasActionErrors(['last']);
    }

    public function testItDoesntCreateARangeIfUnitEmpty()
    {
        Livewire::test(ManageUnitDiscreteSquawkRanges::class)
            ->callAction(
                'create',
                [
                    'first' => '1234',
                    'last' => '2345',
                ]
            )
            ->assertHasActionErrors(['unit']);
    }

    public function testItDoesntCreateARangeIfUnitTooShort()
    {
        Livewire::test(ManageUnitDiscreteSquawkRanges::class)
            ->callAction(
                'create',
                [
                    'first' => '1234',
                    'last' => '2345',
                    'unit' => '',
                ]
            )
            ->assertHasActionErrors(['unit']);
    }

    public function testItDoesntCreateARangeIfUnitTooLong()
    {
        Livewire::test(ManageUnitDiscreteSquawkRanges::class)
            ->callAction(
                'create',
                [
                    'first' => '1234',
                    'last' => '2345',
                    'unit' => Str::padRight('', 256, 'a'),
                ]
            )
            ->assertHasActionErrors(['unit']);
    }

    public function testItLoadsARangeWithRulesForEdit()
    {
        $rule = UnitDiscreteSquawkRange::findOrFail(2);
        $rule->rules = [
            ['type' => 'FLIGHT_RULES', 'rule' => 'VFR'],
            ['type' => 'SERVICE', 'rule' => 'BASIC'],
            ['type' => 'UNIT_TYPE', 'rule' => 'APP'],
        ];
        $rule->save();

        Livewire::test(ManageUnitDiscreteSquawkRanges::class)
            ->mountTableAction(
                'edit',
                UnitDiscreteSquawkRange::findOrFail(2),
            )
            ->assertTableActionDataSet(
                [
                    'id' => 2,
                    'first' => '2342',
                    'last' => '4252',
                    'unit' => 'SCO',
                    'flight_rules' => 'VFR',
                    'unit_type' => 'APP',
                    'service' => 'BASIC',
                ]
            );
    }

    public function testItEditsASquawkRange()
    {
        Livewire::test(ManageUnitDiscreteSquawkRanges::class)
            ->callTableAction(
                'edit',
                UnitDiscreteSquawkRange::findOrFail(1),
                [
                    'first' => '3456',
                    'last' => '4567',
                    'unit' => 'EGKK',
                ]
            )
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'unit_discrete_squawk_ranges',
            [
                'id' => 1,
                'first' => '3456',
                'last' => '4567',
                'unit' => 'EGKK',
            ]
        );
    }

    public function testItEditsASquawkRangeWithRules()
    {
        Livewire::test(ManageUnitDiscreteSquawkRanges::class)
            ->callTableAction(
                'edit',
                UnitDiscreteSquawkRange::findOrFail(1),
                [
                    'first' => '3456',
                    'last' => '4567',
                    'unit' => 'EGKK',
                    'flight_rules' => 'VFR',
                    'service' => 'BASIC',
                    'unit_type' => 'APP',
                ]
            )
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'unit_discrete_squawk_ranges',
            [
                'id' => 1,
                'first' => '3456',
                'last' => '4567',
                'unit' => 'EGKK',
            ]
        );
        $this->assertEquals(
            [
                ['type' => 'SERVICE', 'rule' => 'BASIC'],
                ['type' => 'FLIGHT_RULES', 'rule' => 'VFR'],
                ['type' => 'UNIT_TYPE', 'rule' => 'APP'],
            ],
            UnitDiscreteSquawkRange::find(1)->rules
        );
    }

    public function testItDoesntEditARangeIfFirstInvalid()
    {
        Livewire::test(ManageUnitDiscreteSquawkRanges::class)
            ->callTableAction(
                'edit',
                UnitDiscreteSquawkRange::findOrFail(1),
                [
                    'first' => '234a',
                    'last' => '4567',
                    'unit' => 'EGKK',
                ]
            )
            ->assertHasTableActionErrors(['first']);
    }

    public function testItDoesntEditARangeIfLastInvalid()
    {
        Livewire::test(ManageUnitDiscreteSquawkRanges::class)
            ->callTableAction(
                'edit',
                UnitDiscreteSquawkRange::findOrFail(1),
                [
                    'first' => '2345',
                    'last' => '456a',
                    'unit' => 'EGKK',
                ]
            )
            ->assertHasTableActionErrors(['last']);
    }

    public function testItDoesntEditARangeIfUnitTooShort()
    {
        Livewire::test(ManageUnitDiscreteSquawkRanges::class)
            ->callTableAction(
                'edit',
                UnitDiscreteSquawkRange::findOrFail(1),
                [
                    'first' => '2345',
                    'last' => '4567',
                    'unit' => '',
                ]
            )
            ->assertHasTableActionErrors(['unit']);
    }

    public function testItDoesntEditARangeIfUnitTooLong()
    {
        Livewire::test(ManageUnitDiscreteSquawkRanges::class)
            ->callTableAction(
                'edit',
                UnitDiscreteSquawkRange::findOrFail(1),
                [
                    'first' => '1234',
                    'last' => '2345',
                    'unit' => Str::padRight('', 256, 'a'),
                ]
            )
            ->assertHasTableActionErrors(['unit']);
    }

    protected function getCreateText(): string
    {
        return 'Create unit discrete squawk range';
    }

    protected function getIndexText(): array
    {
        return ['Unit Discrete Squawk Ranges'];
    }

    protected static function resourceClass(): string
    {
        return UnitDiscreteSquawkRangeResource::class;
    }

    protected static function resourceListingClass(): string
    {
        return ManageUnitDiscreteSquawkRanges::class;
    }

    protected static function resourceRecordClass(): string
    {
        return UnitDiscreteSquawkRange::class;
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
