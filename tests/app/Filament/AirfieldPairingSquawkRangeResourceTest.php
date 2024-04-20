<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\AccessCheckingHelpers\ChecksManageRecordsFilamentAccess;
use App\Filament\Resources\AirfieldPairingSquawkRangeResource;
use App\Filament\Resources\AirfieldPairingSquawkRangeResource\Pages\ManageAirfieldPairingSquawkRange;
use App\Models\Squawk\AirfieldPairing\AirfieldPairingSquawkRange;
use Livewire\Livewire;

class AirfieldPairingSquawkRangeResourceTest extends BaseFilamentTestCase
{
    use ChecksManageRecordsFilamentAccess;
    use ChecksDefaultFilamentActionVisibility;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testItCreatesASquawkRange()
    {
        Livewire::test(ManageAirfieldPairingSquawkRange::class)
            ->callAction(
                'create',
                [
                    'first' => '1234',
                    'last' => '2345',
                    'origin' => 'EG',
                    'destination' => 'EGLL',
                ]
            )
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'airfield_pairing_squawk_ranges',
            [
                'first' => '1234',
                'last' => '2345',
                'origin' => 'EG',
                'destination' => 'EGLL',
            ]
        );
    }

    public function testItDoesntCreateARangeIfFirstInvalid()
    {
        Livewire::test(ManageAirfieldPairingSquawkRange::class)
            ->callAction(
                'create',
                [
                    'first' => '123a',
                    'last' => '2345',
                    'origin' => 'EG',
                    'destination' => 'EGLL',
                ]
            )
            ->assertHasActionErrors(['first']);
    }

    public function testItDoesntCreateARangeIfLastInvalid()
    {
        Livewire::test(ManageAirfieldPairingSquawkRange::class)
            ->callAction(
                'create',
                [
                    'first' => '1234',
                    'last' => '234b',
                    'origin' => 'EG',
                    'destination' => 'EGLL',
                ]
            )
            ->assertHasActionErrors(['last']);
    }

    public function testItDoesntCreateARangeIfOriginMissing()
    {
        Livewire::test(ManageAirfieldPairingSquawkRange::class)
            ->callAction(
                'create',
                [
                    'first' => '1234',
                    'last' => '2345',
                    'destination' => 'EGLL',
                ]
            )
            ->assertHasActionErrors(['origin']);
    }

    public function testItDoesntCreateARangeIfOriginInvalid()
    {
        Livewire::test(ManageAirfieldPairingSquawkRange::class)
            ->callAction(
                'create',
                [
                    'first' => '1234',
                    'last' => '2345',
                    'origin' => 'EGA,',
                    'destination' => 'EGLL',
                ]
            )
            ->assertHasActionErrors(['origin']);
    }

    public function testItDoesntCreateARangeIfDestinationMissing()
    {
        Livewire::test(ManageAirfieldPairingSquawkRange::class)
            ->callAction(
                'create',
                [
                    'first' => '1234',
                    'last' => '2345',
                    'origin' => 'EGLL',
                ]
            )
            ->assertHasActionErrors(['destination']);
    }

    public function testItDoesntCreateARangeIfDestinationInvalid()
    {
        Livewire::test(ManageAirfieldPairingSquawkRange::class)
            ->callAction(
                'create',
                [
                    'first' => '1234',
                    'last' => '2345',
                    'origin' => 'EGAA',
                    'destination' => 'EG1.',
                ]
            )
            ->assertHasActionErrors(['destination']);
    }

    public function testItEditsASquawkRange()
    {
        Livewire::test(ManageAirfieldPairingSquawkRange::class)
            ->callTableAction(
                'edit',
                AirfieldPairingSquawkRange::findOrFail(1),
                [
                    'first' => '3456',
                    'last' => '4567',
                    'origin' => 'AF',
                    'destination' => 'EGLL',
                ]
            )
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'airfield_pairing_squawk_ranges',
            [
                'id' => AirfieldPairingSquawkRange::findOrFail(1)->id,
                'first' => '3456',
                'last' => '4567',
                'origin' => 'AF',
                'destination' => 'EGLL',
            ]
        );
    }

    public function testItDoesntEditARangeIfFirstInvalid()
    {
        Livewire::test(ManageAirfieldPairingSquawkRange::class)
            ->callTableAction(
                'edit',
                AirfieldPairingSquawkRange::findOrFail(1),
                [
                    'first' => '345a',
                    'last' => '4567',
                    'origin' => 'AF',
                    'destination' => 'EGLL',
                ]
            )
            ->assertHasTableActionErrors(['first']);
    }

    public function testItDoesntEditARangeIfLastInvalid()
    {
        Livewire::test(ManageAirfieldPairingSquawkRange::class)
            ->callTableAction(
                'edit',
                AirfieldPairingSquawkRange::findOrFail(1),
                [
                    'first' => '3456',
                    'last' => '456a',
                    'origin' => 'AF',
                    'destination' => 'EGLL',
                ]
            )
            ->assertHasTableActionErrors(['last']);
    }

    public function testItDoesntEditARangeIfOriginMissing()
    {
        Livewire::test(ManageAirfieldPairingSquawkRange::class)
            ->callTableAction(
                'edit',
                AirfieldPairingSquawkRange::findOrFail(1),
                [
                    'first' => '3456',
                    'last' => '4567',
                    'destination' => 'EGLL',
                ]
            )
            ->assertHasTableActionErrors(['origin']);
    }

    public function testItDoesntEditARangeIfOriginInvalid()
    {
        Livewire::test(ManageAirfieldPairingSquawkRange::class)
            ->callTableAction(
                'edit',
                AirfieldPairingSquawkRange::findOrFail(1),
                [
                    'first' => '3456',
                    'last' => '4567',
                    'origin' => 'AAAAAAAAAA',
                    'destination' => 'EGLL',
                ]
            )
            ->assertHasTableActionErrors(['origin']);
    }

    public function testItDoesntEditARangeIfDestinationMissing()
    {
        Livewire::test(ManageAirfieldPairingSquawkRange::class)
            ->callTableAction(
                'edit',
                AirfieldPairingSquawkRange::findOrFail(1),
                [
                    'first' => '3456',
                    'last' => '4567',
                    'origin' => 'EGLL',
                ]
            )
            ->assertHasTableActionErrors(['destination']);
    }

    public function testItDoesntEditARangeIfDestinationInvalid()
    {
        Livewire::test(ManageAirfieldPairingSquawkRange::class)
            ->callTableAction(
                'edit',
                AirfieldPairingSquawkRange::findOrFail(1),
                [
                    'first' => '3456',
                    'last' => '4567',
                    'origin' => 'EGLL',
                    'destination' => 'EG1.',
                ]
            )
            ->assertHasTableActionErrors(['destination']);
    }

    protected function getCreateText(): string
    {
        return 'Create airfield pairing squawk range';
    }

    protected function getIndexText(): array
    {
        return ['Airfield Pairing Squawk Ranges'];
    }

    protected static function resourceClass(): string
    {
        return AirfieldPairingSquawkRangeResource::class;
    }

    protected static function resourceListingClass(): string
    {
        return ManageAirfieldPairingSquawkRange::class;
    }

    protected static function resourceRecordClass(): string
    {
        return AirfieldPairingSquawkRange::class;
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
