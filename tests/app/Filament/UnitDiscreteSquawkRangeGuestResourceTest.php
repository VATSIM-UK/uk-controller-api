<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\AccessCheckingHelpers\ChecksManageRecordsFilamentAccess;
use App\Filament\Resources\UnitDiscreteSquawkRangeGuestResource;
use App\Filament\Resources\UnitDiscreteSquawkRangeGuestResource\Pages\ManageUnitDiscreteSquawkRangeGuests;
use App\Models\Squawk\UnitDiscrete\UnitDiscreteSquawkRangeGuest;
use Illuminate\Support\Str;
use Livewire\Livewire;

class UnitDiscreteSquawkRangeGuestResourceTest extends BaseFilamentTestCase
{
    use ChecksManageRecordsFilamentAccess;
    use ChecksDefaultFilamentActionVisibility;

    public function testItCreatesASquawkRangeGuest()
    {
        Livewire::test(ManageUnitDiscreteSquawkRangeGuests::class)
            ->callAction(
                'create',
                [
                    'primary_unit' => 'LON',
                    'guest_unit' => 'LTC',
                ]
            )
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'unit_discrete_squawk_range_guests',
            [
                'primary_unit' => 'LON',
                'guest_unit' => 'LTC',
            ]
        );
    }

    public function testItDoesntCreateAGuestIfPrimaryUnitEmpty()
    {
        Livewire::test(ManageUnitDiscreteSquawkRangeGuests::class)
            ->callAction(
                'create',
                [
                    'guest_unit' => 'LTC',
                ]
            )
            ->assertHasActionErrors(['primary_unit']);
    }

    public function testItDoesntCreateAGuestIfPrimaryUnitTooShort()
    {
        Livewire::test(ManageUnitDiscreteSquawkRangeGuests::class)
            ->callAction(
                'create',
                [
                    'primary_unit' => '',
                    'guest_unit' => 'LTC',
                ]
            )
            ->assertHasActionErrors(['primary_unit']);
    }

    public function testItDoesntCreateAGuestIfPrimaryUnitTooLong()
    {
        Livewire::test(ManageUnitDiscreteSquawkRangeGuests::class)
            ->callAction(
                'create',
                [
                    'primary_unit' => Str::padLeft('', 256, 'a'),
                    'guest_unit' => 'LTC',
                ]
            )
            ->assertHasActionErrors(['primary_unit']);
    }

    public function testItDoesntCreateAGuestIfGuestUnitEmpty()
    {
        Livewire::test(ManageUnitDiscreteSquawkRangeGuests::class)
            ->callAction(
                'create',
                [
                    'primary_unit' => 'LON',
                ]
            )
            ->assertHasActionErrors(['guest_unit']);
    }

    public function testItDoesntCreateAGuestIfGuestUnitTooShort()
    {
        Livewire::test(ManageUnitDiscreteSquawkRangeGuests::class)
            ->callAction(
                'create',
                [
                    'primary_unit' => 'LON',
                    'guest_unit' => 'LT',
                ]
            )
            ->assertHasActionErrors(['guest_unit']);
    }

    public function testItDoesntCreateAGuestIfGuestUnitTooLong()
    {
        Livewire::test(ManageUnitDiscreteSquawkRangeGuests::class)
            ->callAction(
                'create',
                [
                    'primary_unit' => 'LON',
                    'guest_unit' => Str::padRight('', 256, 'a'),
                ]
            )
            ->assertHasActionErrors(['guest_unit']);
    }

    public function testItEditsASquawkRangeGuest()
    {
        Livewire::test(ManageUnitDiscreteSquawkRangeGuests::class)
            ->callTableAction(
                'edit',
                UnitDiscreteSquawkRangeGuest::findOrFail(1),
                [
                    'primary_unit' => 'SCO',
                    'guest_unit' => 'STC',
                ]
            )
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'unit_discrete_squawk_range_guests',
            [
                'id' => 1,
                'primary_unit' => 'SCO',
                'guest_unit' => 'STC',
            ]
        );
    }

    public function testItDoesntEditAGuestIfPrimaryUnitEmpty()
    {
        Livewire::test(ManageUnitDiscreteSquawkRangeGuests::class)
            ->callTableAction(
                'edit',
                UnitDiscreteSquawkRangeGuest::findOrFail(1),
                [
                    'guest_unit' => 'LTC',
                ]
            )
            ->assertHasTableActionErrors(['primary_unit']);
    }

    public function testItDoesntEditAGuestIfPrimaryUnitTooShort()
    {
        Livewire::test(ManageUnitDiscreteSquawkRangeGuests::class)
            ->callTableAction(
                'edit',
                UnitDiscreteSquawkRangeGuest::findOrFail(1),
                [
                    'primary_unit' => '',
                    'guest_unit' => 'LTC',
                ]
            )
            ->assertHasTableActionErrors(['primary_unit']);
    }

    public function testItDoesntEditAGuestIfPrimaryUnitTooLong()
    {
        Livewire::test(ManageUnitDiscreteSquawkRangeGuests::class)
            ->callTableAction(
                'edit',
                UnitDiscreteSquawkRangeGuest::findOrFail(1),
                [
                    'primary_unit' => Str::padLeft('', 256, 'a'),
                    'guest_unit' => 'LTC',
                ]
            )
            ->assertHasTableActionErrors(['primary_unit']);
    }

    public function testItDoesntEditAGuestIfGuestUnitEmpty()
    {
        Livewire::test(ManageUnitDiscreteSquawkRangeGuests::class)
            ->callTableAction(
                'edit',
                UnitDiscreteSquawkRangeGuest::findOrFail(1),
                [
                    'primary_unit' => 'LON',
                ]
            )
            ->assertHasTableActionErrors(['guest_unit']);
    }

    public function testItDoesntEditAGuestIfGuestUnitTooShort()
    {
        Livewire::test(ManageUnitDiscreteSquawkRangeGuests::class)
            ->callTableAction(
                'edit',
                UnitDiscreteSquawkRangeGuest::findOrFail(1),
                [
                    'primary_unit' => 'LON',
                    'guest_unit' => 'LT',
                ]
            )
            ->assertHasTableActionErrors(['guest_unit']);
    }

    public function testItDoesntEditAGuestIfGuestUnitTooLong()
    {
        Livewire::test(ManageUnitDiscreteSquawkRangeGuests::class)
            ->callTableAction(
                'edit',
                UnitDiscreteSquawkRangeGuest::findOrFail(1),
                [
                    'primary_unit' => 'LON',
                    'guest_unit' => Str::padRight('', 256, 'a'),
                ]
            )
            ->assertHasTableActionErrors(['guest_unit']);
    }

    protected function getCreateText(): string
    {
        return 'Create unit discrete squawk range guest';
    }

    protected function getIndexText(): array
    {
        return ['Unit Discrete Squawk Range Guests'];
    }

    protected static function resourceClass(): string
    {
        return UnitDiscreteSquawkRangeGuestResource::class;
    }

    protected static function resourceListingClass(): string
    {
        return ManageUnitDiscreteSquawkRangeGuests::class;
    }

    protected static function resourceRecordClass(): string
    {
        return UnitDiscreteSquawkRangeGuest::class;
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
