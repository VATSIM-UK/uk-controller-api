<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\Resources\TerminalResource;
use App\Filament\Resources\TerminalResource\Pages\CreateTerminal;
use App\Filament\Resources\TerminalResource\Pages\ListTerminals;
use App\Filament\Resources\TerminalResource\Pages\ViewTerminal;
use App\Models\Airfield\Terminal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Livewire\Livewire;

class TerminalResourceTest extends BaseFilamentTestCase
{
    use ChecksDefaultFilamentActionVisibility;
    use ChecksDefaultFilamentAccess;

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

    protected function resourceClass(): string
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
        return [];
    }

    protected static function tableActionRecordId(): array
    {
        return [];
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
        return [
            'view',
        ];
    }
}
