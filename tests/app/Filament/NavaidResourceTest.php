<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\Resources\NavaidResource;
use App\Models\Navigation\Navaid;
use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

class NavaidResourceTest extends BaseFilamentTestCase
{
    use ChecksDefaultFilamentAccess;

    public function testItLoadsDataForView()
    {
        Livewire::test(NavaidResource\Pages\ViewNavaid::class, ['record' => 'WILLO'])
            ->assertSet('data.identifier', 'WILLO')
            ->assertSet('data.latitude', 50.9850000)
            ->assertSet('data.longitude', -0.1916667);
    }

    public function testItCreatesANavaid()
    {
        Livewire::test(NavaidResource\Pages\CreateNavaid::class)
            ->set('data.identifier', 'NOVMA')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'navaids',
            [
                'identifier' => 'NOVMA',
                'latitude' => 12.3,
                'longitude' => 45.6,
            ]
        );
    }

    public function testItCreatesANavaidMaxValues()
    {
        Livewire::test(NavaidResource\Pages\CreateNavaid::class)
            ->set('data.identifier', 'NOVMA')
            ->set('data.latitude', 90)
            ->set('data.longitude', 180)
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'navaids',
            [
                'identifier' => 'NOVMA',
                'latitude' => 90,
                'longitude' => 180,
            ]
        );
    }

    public function testItCreatesANavaidMinValues()
    {
        Livewire::test(NavaidResource\Pages\CreateNavaid::class)
            ->set('data.identifier', 'NOVMA')
            ->set('data.latitude', -90)
            ->set('data.longitude', -180)
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'navaids',
            [
                'identifier' => 'NOVMA',
                'latitude' => -90,
                'longitude' => -180,
            ]
        );
    }

    public function testItDoesntCreateANavaidDuplicateIdentifier()
    {
        Livewire::test(NavaidResource\Pages\CreateNavaid::class)
            ->set('data.identifier', 'WILLO')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->call('create')
            ->assertHasErrors(['data.identifier']);
    }

    public function testItDoesntCreateANavaidIdentifierTooLong()
    {
        Livewire::test(NavaidResource\Pages\CreateNavaid::class)
            ->set('data.identifier', 'WILLO2')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->call('create')
            ->assertHasErrors(['data.identifier']);
    }

    public function testItDoesntCreateANavaidLatitudeTooSmall()
    {
        Livewire::test(NavaidResource\Pages\CreateNavaid::class)
            ->set('data.identifier', 'WILLO')
            ->set('data.latitude', -91)
            ->set('data.longitude', 45.6)
            ->call('create')
            ->assertHasErrors(['data.latitude']);
    }

    public function testItDoesntCreateANavaidLatitudeTooLarge()
    {
        Livewire::test(NavaidResource\Pages\CreateNavaid::class)
            ->set('data.identifier', 'WILLO')
            ->set('data.latitude', 91)
            ->set('data.longitude', 45.6)
            ->call('create')
            ->assertHasErrors(['data.latitude']);
    }

    public function testItDoesntCreateANavaidLongitudeTooSmall()
    {
        Livewire::test(NavaidResource\Pages\CreateNavaid::class)
            ->set('data.identifier', 'WILLO')
            ->set('data.latitude', 15)
            ->set('data.longitude', -181)
            ->call('create')
            ->assertHasErrors(['data.longitude']);
    }

    public function testItDoesntCreateANavaidLongitudeTooLarge()
    {
        Livewire::test(NavaidResource\Pages\CreateNavaid::class)
            ->set('data.identifier', 'WILLO')
            ->set('data.latitude', 15)
            ->set('data.longitude', 181)
            ->call('create')
            ->assertHasErrors(['data.longitude']);
    }

    public function testItEditsANavaid()
    {
        Livewire::test(NavaidResource\Pages\EditNavaid::class, ['record' => 'WILLO'])
            ->set('data.identifier', 'NOVMA')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'navaids',
            [
                'identifier' => 'NOVMA',
                'latitude' => 12.3,
                'longitude' => 45.6,
            ]
        );
    }

    public function testItEditsANavaidMaxValues()
    {
        Livewire::test(NavaidResource\Pages\EditNavaid::class, ['record' => 'WILLO'])
            ->set('data.identifier', 'NOVMA')
            ->set('data.latitude', 90)
            ->set('data.longitude', 180)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'navaids',
            [
                'id' => 1,
                'identifier' => 'NOVMA',
                'latitude' => 90,
                'longitude' => 180,
            ]
        );
    }

    public function testItEditsANavaidMinValues()
    {
        Livewire::test(NavaidResource\Pages\EditNavaid::class, ['record' => 'WILLO'])
            ->set('data.identifier', 'NOVMA')
            ->set('data.latitude', -90)
            ->set('data.longitude', -180)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'navaids',
            [
                'id' => 1,
                'identifier' => 'NOVMA',
                'latitude' => -90,
                'longitude' => -180,
            ]
        );
    }

    public function testItDoesntEditANavaidIdentifierTooLong()
    {
        Livewire::test(NavaidResource\Pages\EditNavaid::class, ['record' => 'WILLO'])
            ->set('data.identifier', 'WILLO2')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->call('save')
            ->assertHasErrors(['data.identifier']);
    }

    public function testItDoesntEditANavaidDuplicateIdentifier()
    {
        Livewire::test(NavaidResource\Pages\EditNavaid::class, ['record' => 'WILLO'])
            ->set('data.identifier', 'TIMBA')
            ->set('data.latitude', 12.3)
            ->set('data.longitude', 45.6)
            ->call('save')
            ->assertHasErrors(['data.identifier']);
    }

    public function testItDoesntEditANavaidLatitudeTooSmall()
    {
        Livewire::test(NavaidResource\Pages\EditNavaid::class, ['record' => 'WILLO'])
            ->set('data.identifier', 'WILLO')
            ->set('data.latitude', -91)
            ->set('data.longitude', 45.6)
            ->call('save')
            ->assertHasErrors(['data.latitude']);
    }

    public function testItDoesntEditANavaidLatitudeTooLarge()
    {
        Livewire::test(NavaidResource\Pages\EditNavaid::class, ['record' => 'WILLO'])
            ->set('data.identifier', 'WILLO')
            ->set('data.latitude', 91)
            ->set('data.longitude', 45.6)
            ->call('save')
            ->assertHasErrors(['data.latitude']);
    }

    public function testItDoesntEditANavaidLongitudeTooSmall()
    {
        Livewire::test(NavaidResource\Pages\EditNavaid::class, ['record' => 'WILLO'])
            ->set('data.identifier', 'WILLO')
            ->set('data.latitude', 15)
            ->set('data.longitude', -181)
            ->call('save')
            ->assertHasErrors(['data.longitude']);
    }

    public function testItDoesntEditANavaidLongitudeTooLarge()
    {
        Livewire::test(NavaidResource\Pages\EditNavaid::class, ['record' => 'WILLO'])
            ->set('data.identifier', 'WILLO')
            ->set('data.latitude', 15)
            ->set('data.longitude', 181)
            ->call('save')
            ->assertHasErrors(['data.longitude']);
    }

    protected function getViewEditRecord(): Model
    {
        return Navaid::findOrFail(1);
    }

    protected function getResourceClass(): string
    {
        return NavaidResource::class;
    }

    protected function getEditText(): string
    {
        return 'Edit WILLO';
    }

    protected function getCreateText(): string
    {
        return 'Create navaid';
    }

    protected function getViewText(): string
    {
        return 'View WILLO';
    }

    protected function getIndexText(): array
    {
        return ['WILLO', 'TIMBA', 'MAY'];
    }
}
