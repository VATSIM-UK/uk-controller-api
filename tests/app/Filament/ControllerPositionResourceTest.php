<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\Resources\ControllerPositionResource;
use App\Filament\Resources\ControllerPositionResource\Pages\ListControllerPositions;
use App\Models\Controller\ControllerPosition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Livewire\Livewire;

class ControllerPositionResourceTest extends BaseFilamentTestCase
{
    use ChecksDefaultFilamentAccess;
    use ChecksDefaultFilamentActionVisibility;

    public function testItLoadsDataForView()
    {
        Livewire::test(ControllerPositionResource\Pages\ViewControllerPosition::class, ['record' => 1])
            ->assertSet('data.callsign', 'EGLL_S_TWR')
            ->assertSet('data.frequency', '118.500')
            ->assertSet('data.requests_departure_releases', true)
            ->assertSet('data.receives_departure_releases', false)
            ->assertSet('data.sends_prenotes', true)
            ->assertSet('data.receives_prenotes', false);
    }

    public function testItCreatesAControllerPositionWithMinimumData()
    {
        Livewire::test(ControllerPositionResource\Pages\CreateControllerPosition::class)
            ->set('data.callsign', 'LON_W_CTR')
            ->set('data.description', '')
            ->set('data.frequency', '126.075')
            ->set('data.requests_departure_releases', true)
            ->set('data.receives_departure_releases', true)
            ->set('data.sends_prenotes', false)
            ->set('data.receives_prenotes', true)
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'controller_positions',
            [
                'callsign' => 'LON_W_CTR',
                'description' => null,
                'frequency' => '126.075',
                'requests_departure_releases' => 1,
                'receives_departure_releases' => 1,
                'sends_prenotes' => 0,
                'receives_prenotes' => 1,
            ]
        );
    }

    public function testItCreatesAControllerPositionWithAllData()
    {
        Livewire::test(ControllerPositionResource\Pages\CreateControllerPosition::class)
            ->set('data.callsign', 'LON_W_CTR')
            ->set('data.description', 'London West (Bandbox)')
            ->set('data.frequency', '126.075')
            ->set('data.requests_departure_releases', true)
            ->set('data.receives_departure_releases', true)
            ->set('data.sends_prenotes', false)
            ->set('data.receives_prenotes', true)
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'controller_positions',
            [
                'callsign' => 'LON_W_CTR',
                'description' => 'London West (Bandbox)',
                'frequency' => '126.075',
                'requests_departure_releases' => 1,
                'receives_departure_releases' => 1,
                'sends_prenotes' => 0,
                'receives_prenotes' => 1,
            ]
        );
    }

    public function testItCreatesAControllerPositionWith8Point33Spacing()
    {
        Livewire::test(ControllerPositionResource\Pages\CreateControllerPosition::class)
            ->set('data.callsign', 'LON_W_CTR')
            ->set('data.description', 'London West (Bandbox)')
            ->set('data.frequency', '126.080')
            ->set('data.requests_departure_releases', true)
            ->set('data.receives_departure_releases', true)
            ->set('data.sends_prenotes', false)
            ->set('data.receives_prenotes', true)
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'controller_positions',
            [
                'callsign' => 'LON_W_CTR',
                'description' => 'London West (Bandbox)',
                'frequency' => '126.080',
                'requests_departure_releases' => 1,
                'receives_departure_releases' => 1,
                'sends_prenotes' => 0,
                'receives_prenotes' => 1,
            ]
        );
    }

    public function testItDoesntCreateAPositionDuplicateCallsign()
    {
        Livewire::test(ControllerPositionResource\Pages\CreateControllerPosition::class)
            ->set('data.callsign', 'EGLL_S_TWR')
            ->set('data.frequency', '126.075')
            ->set('data.requests_departure_releases', true)
            ->set('data.receives_departure_releases', true)
            ->set('data.sends_prenotes', false)
            ->set('data.receives_prenotes', true)
            ->call('create')
            ->assertHasErrors(['data.callsign']);
    }

    public function testItDoesntCreateAPositionDescriptionTooLong()
    {
        Livewire::test(ControllerPositionResource\Pages\CreateControllerPosition::class)
            ->set('data.callsign', 'LON_W_CTR')
            ->set('data.description', Str::padRight('', 256, 'a'))
            ->set('data.frequency', '126.075')
            ->set('data.requests_departure_releases', true)
            ->set('data.receives_departure_releases', true)
            ->set('data.sends_prenotes', false)
            ->set('data.receives_prenotes', true)
            ->call('create')
            ->assertHasErrors(['data.description']);
    }

    public function testItDoesntCreateAPositionInvalidFrequency()
    {
        Livewire::test(ControllerPositionResource\Pages\CreateControllerPosition::class)
            ->set('data.callsign', 'LON_W_CTR')
            ->set('data.frequency', '126.111')
            ->set('data.requests_departure_releases', true)
            ->set('data.receives_departure_releases', true)
            ->set('data.sends_prenotes', false)
            ->set('data.receives_prenotes', true)
            ->call('create')
            ->assertHasErrors(['data.frequency']);
    }

    public function testItEditsAControllerPosition()
    {
        Livewire::test(ControllerPositionResource\Pages\EditControllerPosition::class, ['record' => 1])
            ->set('data.callsign', 'EGLL_S_TWR')
            ->set('data.description', '')
            ->set('data.frequency', '126.075')
            ->set('data.requests_departure_releases', false)
            ->set('data.receives_departure_releases', false)
            ->set('data.sends_prenotes', true)
            ->set('data.receives_prenotes', false)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'controller_positions',
            [
                'callsign' => 'EGLL_S_TWR',
                'description' => null,
                'frequency' => '126.075',
                'requests_departure_releases' => false,
                'receives_departure_releases' => false,
                'sends_prenotes' => true,
                'receives_prenotes' => false,
            ]
        );
    }

    public function testItEditsAControllerPositionAllData()
    {
        Livewire::test(ControllerPositionResource\Pages\EditControllerPosition::class, ['record' => 1])
            ->set('data.callsign', 'EGLL_S_TWR')
            ->set('data.description', 'Foo')
            ->set('data.frequency', '126.075')
            ->set('data.requests_departure_releases', false)
            ->set('data.receives_departure_releases', false)
            ->set('data.sends_prenotes', true)
            ->set('data.receives_prenotes', false)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'controller_positions',
            [
                'callsign' => 'EGLL_S_TWR',
                'description' => 'Foo',
                'frequency' => '126.075',
                'requests_departure_releases' => false,
                'receives_departure_releases' => false,
                'sends_prenotes' => true,
                'receives_prenotes' => false,
            ]
        );
    }

    public function testItDoesntEditAPositionDuplicateCallsign()
    {
        Livewire::test(ControllerPositionResource\Pages\EditControllerPosition::class, ['record' => 1])
            ->set('data.callsign', 'EGLL_N_APP')
            ->call('save')
            ->assertHasErrors(['data.callsign']);
    }

    public function testItDoesntEditAPositionDescriptionTooLong()
    {
        Livewire::test(ControllerPositionResource\Pages\EditControllerPosition::class, ['record' => 1])
            ->set('data.description', Str::padRight('', 256, 'a'))
            ->call('save')
            ->assertHasErrors(['data.description']);
    }

    public function testItDoesntEditAPositionInvalidFrequency()
    {
        Livewire::test(ControllerPositionResource\Pages\EditControllerPosition::class, ['record' => 1])
            ->set('data.frequency', '126.111')
            ->call('save')
            ->assertHasErrors(['data.frequency']);
    }

    protected static function resourceClass(): string
    {
        return ControllerPositionResource::class;
    }

    protected function getEditText(): string
    {
        return 'Edit EGLL_S_TWR';
    }

    protected function getCreateText(): string
    {
        return 'Create Controller Position';
    }

    protected function getViewText(): string
    {
        return 'View EGLL_S_TWR';
    }

    protected function getIndexText(): array
    {
        return ['EGLL_S_TWR', '118.500', 'EGLL_N_APP', '119.725'];
    }

    protected static function resourceId(): int|string
    {
        return 1;
    }

    protected static function resourceRecordClass(): string
    {
        return ControllerPosition::class;
    }

    protected static function resourceListingClass(): string
    {
        return ListControllerPositions::class;
    }

    protected static function writeResourceTableActions(): array
    {
        return [
            'edit',
        ];
    }

    protected static function readOnlyResourceTableActions(): array
    {
        return [
            'view',
        ];
    }

    protected static function writeResourcePageActions(): array
    {
        return [
            'create',
        ];
    }

    protected function getEditRecord(): Model
    {
        return ControllerPosition::findOrFail(1);
    }

    protected function getViewRecord(): Model
    {
        return ControllerPosition::findOrFail(1);
    }
}
