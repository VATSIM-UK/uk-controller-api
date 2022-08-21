<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\Resources\ControllerPositionResource;
use App\Filament\Resources\ControllerPositionResource\Pages\ListControllerPositions;
use App\Models\Controller\ControllerPosition;
use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

class ControllerPositionResourceTest extends BaseFilamentTestCase
{
    use ChecksDefaultFilamentAccess;
    use ChecksFilamentTableActionAccess;
    use ChecksFilamentReadOnlyTableActionAccess;

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

    public function testItCreatesAControllerPosition()
    {
        Livewire::test(ControllerPositionResource\Pages\CreateControllerPosition::class)
            ->set('data.callsign', 'LON_W_CTR')
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
                'frequency' => '126.075',
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

    public function testItDoesntEditAPositionInvalidFrequency()
    {
        Livewire::test(ControllerPositionResource\Pages\EditControllerPosition::class, ['record' => 1])
            ->set('data.frequency', '126.111')
            ->call('save')
            ->assertHasErrors(['data.frequency']);
    }

    protected function getViewEditRecord(): Model
    {
        return ControllerPosition::findOrFail(1);
    }

    protected function getResourceClass(): string
    {
        return ControllerPositionResource::class;
    }

    protected function getEditText(): string
    {
        return 'Edit EGLL_S_TWR';
    }

    protected function getCreateText(): string
    {
        return 'Create controller position';
    }

    protected function getViewText(): string
    {
        return 'View EGLL_S_TWR';
    }

    protected function getIndexText(): array
    {
        return ['EGLL_S_TWR', '118.500', 'EGLL_N_APP', '119.725'];
    }

    protected function tableActionRecordClass(): array
    {
        return [ListControllerPositions::class => ControllerPosition::class];
    }

    protected function tableActionRecordId(): int|string
    {
        return 1;
    }

    protected function tableActionOwnerRecordClass(): string
    {
        return ControllerPosition::class;
    }

    protected function tableActionOwnerRecordId(): string
    {
        return 1;
    }

    protected function writeTableActions(): array
    {
        return [
            ListControllerPositions::class => [
                'edit',
                'create',
            ],
        ];
    }

    protected function readOnlyTableActions(): array
    {
        return [
            ListControllerPositions::class => [
                'view',
            ],
        ];
    }
}
