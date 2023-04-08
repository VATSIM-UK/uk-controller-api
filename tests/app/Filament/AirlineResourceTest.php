<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\Resources\AirlineResource;
use App\Filament\Resources\AirlineResource\Pages\CreateAirline;
use App\Filament\Resources\AirlineResource\Pages\EditAirline;
use App\Filament\Resources\AirlineResource\Pages\ListAirlines;
use App\Filament\Resources\AirlineResource\Pages\ViewAirline;
use App\Models\Airline\Airline;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Livewire\Livewire;

class AirlineResourceTest extends BaseFilamentTestCase
{
    use ChecksOperationsContributorActionVisibility;
    use ChecksOperationsContributorAccess;

    public function testItLoadsDataForView()
    {
        Livewire::test(ViewAirline::class, ['record' => 1])
            ->assertSet('data.icao_code', 'BAW')
            ->assertSet('data.name', 'British Airways')
            ->assertSet('data.callsign', 'SPEEDBIRD')
            ->assertSet('data.is_cargo', false);
    }

    public function testItCreatesAnAirline()
    {
        Livewire::test(CreateAirline::class)
            ->set('data.icao_code', 'EZY')
            ->set('data.name', 'EasyJet')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'airlines',
            [
                'icao_code' => 'EZY',
                'name' => 'EasyJet',
                'callsign' => 'EASY',
                'is_cargo' => false,
            ]
        );
    }

    public function testItCreatesACargoAirline()
    {
        Livewire::test(CreateAirline::class)
            ->set('data.icao_code', 'EZY')
            ->set('data.name', 'EasyJet Cargo')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', true)
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'airlines',
            [
                'icao_code' => 'EZY',
                'name' => 'EasyJet Cargo',
                'callsign' => 'EASY',
                'is_cargo' => true,
            ]
        );
    }

    public function testItDoesntCreateAnAirlineNoIcaoCode()
    {
        Livewire::test(CreateAirline::class)
            ->set('data.name', 'EasyJet')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('create')
            ->assertHasErrors(['data.icao_code']);
    }

    public function testItDoesntCreateAnAirlineIcaoCodeEmpty()
    {
        Livewire::test(CreateAirline::class)
            ->set('data.icao_code', '')
            ->set('data.name', 'EasyJet')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('create')
            ->assertHasErrors(['data.icao_code']);
    }

    public function testItDoesntCreateAnAirlineIcaoCodeTooLong()
    {
        Livewire::test(CreateAirline::class)
            ->set('data.icao_code', Str::padRight('', 256, 'a'))
            ->set('data.name', 'EasyJet')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('create')
            ->assertHasErrors(['data.icao_code']);
    }

    public function testItDoesntCreateAnAirlineNoName()
    {
        Livewire::test(CreateAirline::class)
            ->set('data.icao_code', 'EZY')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('create')
            ->assertHasErrors(['data.name']);
    }

    public function testItDoesntCreateAnAirlineNameEmpty()
    {
        Livewire::test(CreateAirline::class)
            ->set('data.icao_code', 'EZY')
            ->set('data.name', '')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('create')
            ->assertHasErrors(['data.name']);
    }

    public function testItDoesntCreateAnAirlineNameTooLong()
    {
        Livewire::test(CreateAirline::class)
            ->set('data.icao_code', 'EZY')
            ->set('data.name', Str::padRight('', 256, 'a'))
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('create')
            ->assertHasErrors(['data.name']);
    }

    public function testItDoesntCreateAnAirlineNoCallsign()
    {
        Livewire::test(CreateAirline::class)
            ->set('data.icao_code', 'EZY')
            ->set('data.name', 'EasyJet')
            ->set('data.is_cargo', false)
            ->call('create')
            ->assertHasErrors(['data.callsign']);
    }

    public function testItDoesntCreateAnAirlineCallsignEmpty()
    {
        Livewire::test(CreateAirline::class)
            ->set('data.icao_code', 'EZY')
            ->set('data.name', 'EasyJet')
            ->set('data.callsign', '')
            ->set('data.is_cargo', false)
            ->call('create')
            ->assertHasErrors(['data.callsign']);
    }

    public function testItDoesntCreateAnAirlineCallsignTooLong()
    {
        Livewire::test(CreateAirline::class)
            ->set('data.icao_code', 'EZY')
            ->set('data.name', 'EasyJet')
            ->set('data.callsign', Str::padRight('', 256, 'a'))
            ->set('data.is_cargo', false)
            ->call('create')
            ->assertHasErrors(['data.callsign']);
    }

    public function testItLoadsDataForEdit()
    {
        Livewire::test(EditAirline::class, ['record' => 1])
            ->assertSet('data.icao_code', 'BAW')
            ->assertSet('data.name', 'British Airways')
            ->assertSet('data.callsign', 'SPEEDBIRD')
            ->assertSet('data.is_cargo', false);
    }

    public function testItEditsAnAirline()
    {
        Livewire::test(EditAirline::class, ['record' => 1])
            ->set('data.icao_code', 'EZY')
            ->set('data.name', 'EasyJet')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'airlines',
            [
                'id' => 1,
                'icao_code' => 'EZY',
                'name' => 'EasyJet',
                'callsign' => 'EASY',
                'is_cargo' => false,
            ]
        );
    }

    public function testItEditsACargoAirline()
    {
        Livewire::test(EditAirline::class, ['record' => 1])
            ->set('data.icao_code', 'EZY')
            ->set('data.name', 'EasyJet Cargo')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'airlines',
            [
                'id' => 1,
                'icao_code' => 'EZY',
                'name' => 'EasyJet Cargo',
                'callsign' => 'EASY',
                'is_cargo' => true,
            ]
        );
    }

    public function testItDoesntEditAnAirlineNoIcaoCode()
    {
        Livewire::test(EditAirline::class, ['record' => 1])
            ->set('data.icao_code')
            ->set('data.name', 'EasyJet')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('save')
            ->assertHasErrors(['data.icao_code']);
    }

    public function testItDoesntEditAnAirlineIcaoCodeEmpty()
    {
        Livewire::test(EditAirline::class, ['record' => 1])
            ->set('data.icao_code', '')
            ->set('data.name', 'EasyJet')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('save')
            ->assertHasErrors(['data.icao_code']);
    }

    public function testItDoesntEditAnAirlineIcaoCodeTooLong()
    {
        Livewire::test(EditAirline::class, ['record' => 1])
            ->set('data.icao_code', Str::padRight('', 256, 'a'))
            ->set('data.name', 'EasyJet')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('save')
            ->assertHasErrors(['data.icao_code']);
    }

    public function testItDoesntEditAnAirlineNoName()
    {
        Livewire::test(EditAirline::class, ['record' => 1])
            ->set('data.icao_code', 'EZY')
            ->set('data.name')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('save')
            ->assertHasErrors(['data.name']);
    }

    public function testItDoesntEditAnAirlineNameEmpty()
    {
        Livewire::test(EditAirline::class, ['record' => 1])
            ->set('data.icao_code', 'EZY')
            ->set('data.name', '')
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('save')
            ->assertHasErrors(['data.name']);
    }

    public function testItDoesntEditAnAirlineNameTooLong()
    {
        Livewire::test(EditAirline::class, ['record' => 1])
            ->set('data.icao_code', 'EZY')
            ->set('data.name', Str::padRight('', 256, 'a'))
            ->set('data.callsign', 'EASY')
            ->set('data.is_cargo', false)
            ->call('save')
            ->assertHasErrors(['data.name']);
    }

    public function testItDoesntEditAnAirlineNoCallsign()
    {
        Livewire::test(EditAirline::class, ['record' => 1])
            ->set('data.icao_code', 'EZY')
            ->set('data.name', 'EasyJet')
            ->set('data.callsign')
            ->set('data.is_cargo', false)
            ->call('save')
            ->assertHasErrors(['data.callsign']);
    }

    public function testItDoesntEditAnAirlineCallsignEmpty()
    {
        Livewire::test(EditAirline::class, ['record' => 1])
            ->set('data.icao_code', 'EZY')
            ->set('data.name', 'EasyJet')
            ->set('data.callsign', '')
            ->set('data.is_cargo', false)
            ->call('save')
            ->assertHasErrors(['data.callsign']);
    }

    public function testItDoesntEditAnAirlineCallsignTooLong()
    {
        Livewire::test(EditAirline::class, ['record' => 1])
            ->set('data.icao_code', 'EZY')
            ->set('data.name', 'EasyJet')
            ->set('data.callsign', Str::padRight('', 256, 'a'))
            ->set('data.is_cargo', false)
            ->call('save')
            ->assertHasErrors(['data.callsign']);
    }

    protected function getCreateText(): string
    {
        return 'Create Airline';
    }

    protected function getEditRecord(): Model
    {
        return Airline::findOrFail(1);
    }

    protected function getEditText(): string
    {
        return 'Edit BAW';
    }

    protected function getIndexText(): array
    {
        return ['BAW', 'British Airways', 'SPEEDBIRD'];
    }

    protected function getViewText(): string
    {
        return 'View BAW';
    }

    protected function getViewRecord(): Model
    {
        return $this->getEditRecord();
    }

    protected function resourceClass(): string
    {
        return AirlineResource::class;
    }

    protected static function resourceRecordClass(): string
    {
        return Airline::class;
    }

    protected static function resourceId(): int|string
    {
        return 1;
    }

    protected static function writeResourcePageActions(): array
    {
        return [
            'create',
        ];
    }

    protected static function resourceListingClass(): string
    {
        return ListAirlines::class;
    }

    protected static function tableActionRecordClass(): array
    {
        return [
        ];
    }

    protected static function tableActionRecordId(): array
    {
        return [
        ];
    }

    protected static function writeTableActions(): array
    {
        return [
        ];
    }

    protected static function writeResourceTableActions(): array
    {
        return [
            'edit',
        ];
    }

    protected static function readOnlyResourceTableActions(): array
    {
        return ['view'];
    }
}
