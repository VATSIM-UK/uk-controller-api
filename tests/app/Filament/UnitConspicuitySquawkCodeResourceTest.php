<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\AccessCheckingHelpers\ChecksManageRecordsFilamentAccess;
use App\Filament\Resources\UnitConspicuitySquawkCodeResource;
use App\Filament\Resources\UnitConspicuitySquawkCodeResource\Pages\ManageUnitConspicuitySquawkCodes;
use App\Models\Squawk\UnitConspicuity\UnitConspicuitySquawkCode;
use Illuminate\Support\Str;
use Livewire\Livewire;

class UnitConspicuitySquawkCodeResourceTest extends BaseFilamentTestCase
{
    use ChecksManageRecordsFilamentAccess;
    use ChecksDefaultFilamentActionVisibility;

    public function testItCreatesASquawkRange()
    {
        Livewire::test(ManageUnitConspicuitySquawkCodes::class)
            ->callAction(
                'create',
                [
                    'code' => '1234',
                    'unit' => 'EGKK',
                ]
            )
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'unit_conspicuity_squawk_codes',
            [
                'code' => '1234',
                'unit' => 'EGKK',
            ]
        );
    }

    public function testItCreatesASquawkRangeWithRules()
    {
        Livewire::test(ManageUnitConspicuitySquawkCodes::class)
            ->callAction(
                'create',
                [
                    'code' => '1234',
                    'unit' => 'EGKK',
                    'flight_rules' => 'VFR',
                    'service' => 'BASIC',
                    'unit_type' => 'APP',
                ]
            )
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'unit_conspicuity_squawk_codes',
            [
                'code' => '1234',
                'unit' => 'EGKK',
            ]
        );
        $this->assertEquals(
            [
                ['type' => 'SERVICE', 'rule' => 'BASIC'],
                ['type' => 'FLIGHT_RULES', 'rule' => 'VFR'],
                ['type' => 'UNIT_TYPE', 'rule' => 'APP'],
            ],
            UnitConspicuitySquawkCode::latest()->first()->rules
        );
    }

    public function testItDoesntCreateARangeIfCodeInvalid()
    {
        Livewire::test(ManageUnitConspicuitySquawkCodes::class)
            ->callAction(
                'create',
                [
                    'code' => '123a',
                    'unit' => 'EGKK',
                ]
            )
            ->assertHasActionErrors(['code']);
    }

    public function testItDoesntCreateARangeIfCodeMissing()
    {
        Livewire::test(ManageUnitConspicuitySquawkCodes::class)
            ->callAction(
                'create',
                [
                    'code' => null,
                    'unit' => 'EGKK',
                ]
            )
            ->assertHasActionErrors(['code']);
    }

    public function testItDoesntCreateARangeIfUnitEmpty()
    {
        Livewire::test(ManageUnitConspicuitySquawkCodes::class)
            ->callAction(
                'create',
                [
                    'code' => '1234',
                ]
            )
            ->assertHasActionErrors(['unit']);
    }

    public function testItDoesntCreateARangeIfUnitTooShort()
    {
        Livewire::test(ManageUnitConspicuitySquawkCodes::class)
            ->callAction(
                'create',
                [
                    'code' => '1234',
                    'unit' => '',
                ]
            )
            ->assertHasActionErrors(['unit']);
    }

    public function testItDoesntCreateARangeIfUnitTooLong()
    {
        Livewire::test(ManageUnitConspicuitySquawkCodes::class)
            ->callAction(
                'create',
                [
                    'code' => '1234',
                    'unit' => Str::padRight('', 256, 'a'),
                ]
            )
            ->assertHasActionErrors(['unit']);
    }

    public function testItLoadsARangeWithRulesForEdit()
    {
        $rule = UnitConspicuitySquawkCode::findOrFail(1);
        $rule->rules = [
            ['type' => 'FLIGHT_RULES', 'rule' => 'VFR'],
            ['type' => 'SERVICE', 'rule' => 'BASIC'],
            ['type' => 'UNIT_TYPE', 'rule' => 'APP'],
        ];
        $rule->save();

        Livewire::test(ManageUnitConspicuitySquawkCodes::class)
            ->mountTableAction(
                'edit',
                UnitConspicuitySquawkCode::findOrFail(1),
            )
            ->assertTableActionDataSet(
                [
                    'id' => 1,
                    'code' => '7221',
                    'unit' => 'SCO',
                    'flight_rules' => 'VFR',
                    'unit_type' => 'APP',
                    'service' => 'BASIC',
                ]
            );
    }

    public function testItEditsASquawkRange()
    {
        Livewire::test(ManageUnitConspicuitySquawkCodes::class)
            ->callTableAction(
                'edit',
                UnitConspicuitySquawkCode::findOrFail(1),
                [
                    'code' => '2354',
                    'unit' => 'EGKK',
                ]
            )
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'unit_conspicuity_squawk_codes',
            [
                'id' => 1,
                'code' => '2354',
                'unit' => 'EGKK',
            ]
        );
    }

    public function testItEditsASquawkRangeWithRules()
    {
        Livewire::test(ManageUnitConspicuitySquawkCodes::class)
            ->callTableAction(
                'edit',
                UnitConspicuitySquawkCode::findOrFail(1),
                [
                    'code' => '2354',
                    'unit' => 'EGKK',
                    'flight_rules' => 'VFR',
                    'service' => 'BASIC',
                    'unit_type' => 'APP',
                ]
            )
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'unit_conspicuity_squawk_codes',
            [
                'id' => 1,
                'code' => '2354',
                'unit' => 'EGKK',
            ]
        );
        $this->assertEquals(
            [
                ['type' => 'SERVICE', 'rule' => 'BASIC'],
                ['type' => 'FLIGHT_RULES', 'rule' => 'VFR'],
                ['type' => 'UNIT_TYPE', 'rule' => 'APP'],
            ],
            UnitConspicuitySquawkCode::find(1)->rules
        );
    }

    public function testItDoesntEditARangeIfCodeInvalid()
    {
        Livewire::test(ManageUnitConspicuitySquawkCodes::class)
            ->callTableAction(
                'edit',
                UnitConspicuitySquawkCode::findOrFail(1),
                [
                    'code' => '235a',
                    'unit' => 'EGKK',
                ]
            )
            ->assertHasTableActionErrors(['code']);
    }

    public function testItDoesntEditARangeIfCodeMissing()
    {
        Livewire::test(ManageUnitConspicuitySquawkCodes::class)
            ->callTableAction(
                'edit',
                UnitConspicuitySquawkCode::findOrFail(1),
                [
                    'unit' => 'EGKK',
                    'code' => null,
                ]
            )
            ->assertHasTableActionErrors(['code']);
    }

    public function testItDoesntEditARangeIfUnitTooShort()
    {
        Livewire::test(ManageUnitConspicuitySquawkCodes::class)
            ->callTableAction(
                'edit',
                UnitConspicuitySquawkCode::findOrFail(1),
                [
                    'code' => '2354',
                    'unit' => '',
                ]
            )
            ->assertHasTableActionErrors(['unit']);
    }

    public function testItDoesntEditARangeIfUnitTooLong()
    {
        Livewire::test(ManageUnitConspicuitySquawkCodes::class)
            ->callTableAction(
                'edit',
                UnitConspicuitySquawkCode::findOrFail(1),
                [
                    'code' => '2354',
                    'unit' => Str::padRight('', 256, 'a'),
                ]
            )
            ->assertHasTableActionErrors(['unit']);
    }

    protected function getCreateText(): string
    {
        return 'Create unit conspicuity squawk code';
    }

    protected function getIndexText(): array
    {
        return ['Unit Conspicuity Squawk Codes'];
    }

    protected static function resourceClass(): string
    {
        return UnitConspicuitySquawkCodeResource::class;
    }

    protected static function resourceListingClass(): string
    {
        return ManageUnitConspicuitySquawkCodes::class;
    }

    protected static function resourceRecordClass(): string
    {
        return UnitConspicuitySquawkCode::class;
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
