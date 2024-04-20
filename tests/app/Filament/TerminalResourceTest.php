<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\Resources\TerminalResource\RelationManagers\AirlinesRelationManager;
use App\Filament\Resources\TerminalResource;
use App\Filament\Resources\TerminalResource\Pages\CreateTerminal;
use App\Filament\Resources\TerminalResource\Pages\EditTerminal;
use App\Filament\Resources\TerminalResource\Pages\ListTerminals;
use App\Filament\Resources\TerminalResource\Pages\ViewTerminal;
use App\Models\Airfield\Terminal;
use App\Models\Airline\Airline;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Livewire;

class TerminalResourceTest extends BaseFilamentTestCase
{
    use ChecksOperationsContributorActionVisibility;
    use ChecksOperationsContributorAccess;

    public function testItLoadsDataForView()
    {
        Livewire::test(ViewTerminal::class, ['record' => 1])
            ->assertSet('data.airfield_id', 1)
            ->assertSet('data.description', 'Terminal 1');
    }

    public function testItCreatesATerminal()
    {
        Livewire::test(CreateTerminal::class)
            ->set('data.airfield_id', 1)
            ->set('data.description', 'A new terminal')
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'terminals',
            [
                'airfield_id' => 1,
                'description' => 'A new terminal',
            ]
        );
    }

    public function testItDoesntCreateATerminalNoAirfieldId()
    {
        Livewire::test(CreateTerminal::class)
            ->set('data.description', 'A new terminal')
            ->call('create')
            ->assertHasErrors(['data.airfield_id']);
    }

    public function testItDoesntCreateATerminalNoDescription()
    {
        Livewire::test(CreateTerminal::class)
            ->set('data.airfield_id', 1)
            ->call('create')
            ->assertHasErrors(['data.description']);
    }

    public function testItDoesntCreateATerminalDescriptionEmpty()
    {
        Livewire::test(CreateTerminal::class)
            ->set('data.airfield_id', 1)
            ->set('data.description', '')
            ->call('create')
            ->assertHasErrors(['data.description']);
    }

    public function testItDoesntCreateATerminalDescriptionToLong()
    {
        Livewire::test(CreateTerminal::class)
            ->set('data.airfield_id', 1)
            ->set('data.description', Str::padRight('', 256, 'a'))
            ->call('create')
            ->assertHasErrors(['data.description']);
    }

    public function testItLoadsDataForEdit()
    {
        Livewire::test(EditTerminal::class, ['record' => 1])
            ->assertSet('data.airfield_id', 1)
            ->assertSet('data.description', 'Terminal 1');
    }

    public function testItEditsATerminal()
    {
        Livewire::test(EditTerminal::class, ['record' => 1])
            ->set('data.airfield_id', 1)
            ->set('data.description', 'A new terminal 2')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'terminals',
            [
                'id' => 1,
                'airfield_id' => 1,
                'description' => 'A new terminal 2',
            ]
        );
    }

    public function testItDoesntEditATerminalNoAirfieldId()
    {
        Livewire::test(EditTerminal::class, ['record' => 1])
            ->set('data.airfield_id')
            ->set('data.description', 'A new terminal')
            ->call('save')
            ->assertHasErrors(['data.airfield_id']);
    }

    public function testItDoesntEditATerminalNoDescription()
    {
        Livewire::test(EditTerminal::class, ['record' => 1])
            ->set('data.description')
            ->call('save')
            ->assertHasErrors(['data.description']);
    }

    public function testItDoesntEditATerminalDescriptionEmpty()
    {
        Livewire::test(EditTerminal::class, ['record' => 1])
            ->set('data.airfield_id', 1)
            ->set('data.description', '')
            ->call('save')
            ->assertHasErrors(['data.description']);
    }

    public function testItDoesntEditATerminalDescriptionToLong()
    {
        Livewire::test(EditTerminal::class, ['record' => 1])
            ->set('data.airfield_id', 1)
            ->set('data.description', Str::padRight('', 256, 'a'))
            ->call('save')
            ->assertHasErrors(['data.description']);
    }

    public function testItAllowsAirlinePairingWithMinimalData()
    {
        Livewire::test(
            AirlinesRelationManager::class,
            ['ownerRecord' => Terminal::findOrFail(1), 'pageClass' => EditTerminal::class]
        )
            ->callTableAction('pair-airline', data: ['recordId' => 1, 'priority' => 100])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas(
            'airline_terminal',
            [
                'airline_id' => 1,
                'terminal_id' => 1,
                'aircraft_id' => null,
                'destination' => null,
                'priority' => 100,
                'full_callsign' => null,
                'callsign_slug' => null,
            ]
        );
    }

    public function testItAllowsAirlinePairingWithFullData()
    {
        Livewire::test(
            AirlinesRelationManager::class,
            ['ownerRecord' => Terminal::findOrFail(1), 'pageClass' => EditTerminal::class]
        )
            ->callTableAction(
                'pair-airline',
                data: [
                    'recordId' => 1,
                    'aircraft_id' => 1,
                    'destination' => 'EGKK',
                    'priority' => 55,
                    'full_callsign' => 'abcd',
                    'callsign_slug' => '1234',
                ]
            )->assertHasNoTableActionErrors();

        $this->assertDatabaseHas(
            'airline_terminal',
            [
                'airline_id' => 1,
                'terminal_id' => 1,
                'aircraft_id' => 1,
                'destination' => 'EGKK',
                'priority' => 55,
                'full_callsign' => 'abcd',
                'callsign_slug' => '1234',
            ]
        );
    }

    public function testItAllowsAirlinesPairedMultipleTimes()
    {
        Terminal::findOrFail(1)->airlines()->sync([1]);
        Livewire::test(
            AirlinesRelationManager::class,
            ['ownerRecord' => Terminal::findOrFail(1), 'pageClass' => EditTerminal::class]
        )
            ->callTableAction(
                'pair-airline',
                data: [
                    'recordId' => 1,
                    'destination' => 'EGKK',
                    'priority' => 55,
                    'callsign_slug' => '1234',
                ]
            )->assertHasNoTableActionErrors();

        $this->assertDatabaseCount('airline_terminal', 2);
        $this->assertDatabaseHas(
            'airline_terminal',
            [
                'airline_id' => 1,
                'terminal_id' => 1,
                'destination' => 'EGKK',
                'priority' => 55,
                'callsign_slug' => '1234',
            ]
        );
    }

    public function testItAllowsAirlineUnpairing()
    {
        Terminal::findOrFail(1)->airlines()->sync([3, 2, 1]);
        $rowToUnpair = DB::table('airline_terminal')
            ->where('terminal_id', 1)
            ->where('airline_id', 3)
            ->first()
            ->id;

        Livewire::test(
            AirlinesRelationManager::class,
            ['ownerRecord' => Terminal::findOrFail(1), 'pageClass' => EditTerminal::class]
        )
            ->callTableAction('unpair-airline', $rowToUnpair)
            ->assertSuccessful()
            ->assertHasNoTableActionErrors();
        $this->assertEquals([1, 2], Terminal::findOrFail(1)->airlines->pluck('id')->sort()->values()->toArray());
    }

    public function testItFailsAirlinePairingPriorityTooLow()
    {
        Livewire::test(
            AirlinesRelationManager::class,
            ['ownerRecord' => Terminal::findOrFail(1), 'pageClass' => EditTerminal::class]
        )
            ->callTableAction(
                'pair-airline',
                data: [
                    'recordId' => 1,
                    'destination' => 'EGKK',
                    'priority' => -1,
                    'callsign_slug' => '1234',
                ]
            )->assertHasTableActionErrors(['priority']);
    }

    public function testItFailsAirlinePairingPriorityTooHigh()
    {
        Livewire::test(
            AirlinesRelationManager::class,
            ['ownerRecord' => Terminal::findOrFail(1), 'pageClass' => EditTerminal::class]
        )
            ->callTableAction(
                'pair-airline',
                data: [
                    'recordId' => 1,
                    'destination' => 'EGKK',
                    'priority' => 99999,
                    'callsign_slug' => '1234',
                ]
            )->assertHasTableActionErrors(['priority']);
    }

    public function testItFailsAirlinePairingCallsignTooLong()
    {
        Livewire::test(
            AirlinesRelationManager::class,
            ['ownerRecord' => Terminal::findOrFail(1), 'pageClass' => EditTerminal::class]
        )
            ->callTableAction(
                'pair-airline',
                data: [
                    'recordId' => 1,
                    'destination' => 'EGKK',
                    'priority' => 55,
                    'full_callsign' => '12345',
                ]
            )->assertHasTableActionErrors(['full_callsign']);
    }

    public function testItFailsAirlinePairingCallsignSlugTooLong()
    {
        Livewire::test(
            AirlinesRelationManager::class,
            ['ownerRecord' => Terminal::findOrFail(1), 'pageClass' => EditTerminal::class]
        )
            ->callTableAction(
                'pair-airline',
                data: [
                    'recordId' => 1,
                    'destination' => 'EGKK',
                    'priority' => 55,
                    'callsign_slug' => '12345',
                ]
            )->assertHasTableActionErrors(['callsign_slug']);
    }

    public function testItFailsAirlinePairingDestinationTooLong()
    {
        Livewire::test(
            AirlinesRelationManager::class,
            ['ownerRecord' => Terminal::findOrFail(1), 'pageClass' => EditTerminal::class]
        )
            ->callTableAction(
                'pair-airline',
                data: [
                    'recordId' => 1,
                    'destination' => 'EGKKS',
                    'priority' => 55,
                    'callsign_slug' => '1234',
                ]
            )->assertHasTableActionErrors(['destination']);
    }

    protected function getCreateText(): string
    {
        return 'Create Terminal';
    }

    protected function getEditRecord(): Model
    {
        return Terminal::findOrFail(1);
    }

    protected function getEditText(): string
    {
        return 'Edit Terminal 1';
    }

    protected function getIndexText(): array
    {
        return ['Terminals', 'Terminal 1', 'Terminal 2'];
    }

    protected function getViewText(): string
    {
        return 'View Terminal 1';
    }

    protected function getViewRecord(): Model
    {
        return $this->getEditRecord();
    }

    protected static function resourceClass(): string
    {
        return TerminalResource::class;
    }

    protected static function resourceRecordClass(): string
    {
        return Terminal::class;
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
        return ListTerminals::class;
    }

    protected static function tableActionRecordClass(): array
    {
        return [
            AirlinesRelationManager::class => Airline::class,
        ];
    }

    protected static function tableActionRecordId(): array
    {
        return [
            AirlinesRelationManager::class => 1,
        ];
    }

    protected static function writeTableActions(): array
    {
        return [
            AirlinesRelationManager::class => [
                'pair-airline',
                'unpair-airline',
                'edit-airline-pairing',
            ],
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
        return [
            'view',
        ];
    }
}
