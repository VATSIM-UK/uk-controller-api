<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\Resources\PrenoteResource;
use App\Models\Controller\ControllerPosition;
use App\Models\Controller\Prenote;
use App\Services\PrenoteService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Livewire\Livewire;

class PrenoteResourceTest extends BaseFilamentTestCase
{
    use ChecksDefaultFilamentAccess;

    public function testItLoadsDataForView()
    {
        Livewire::test(PrenoteResource\Pages\ViewPrenote::class, ['record' => 1])
            ->assertSet('data.description', 'Prenote One');
    }

    public function testItCreatesAPrenote()
    {
        Livewire::test(PrenoteResource\Pages\CreatePrenote::class)
            ->set('data.description', 'A Prenote')
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'prenotes',
            [
                'description' => 'A Prenote',
            ]
        );
    }

    public function testItDoesntCreatePrenoteIfDescriptionEmpty()
    {
        Livewire::test(PrenoteResource\Pages\CreatePrenote::class)
            ->set('data.description', '')
            ->call('create')
            ->assertHasErrors(['data.description']);
    }

    public function testItDoesntCreatePrenoteIfDescriptionTooLong()
    {
        Livewire::test(PrenoteResource\Pages\CreatePrenote::class)
            ->set('data.description', Str::padRight('', 256, 'a'))
            ->call('create')
            ->assertHasErrors(['data.description']);
    }

    public function testItLoadsDataForEdit()
    {
        Livewire::test(PrenoteResource\Pages\EditPrenote::class, ['record' => 1])
            ->assertSet('data.description', 'Prenote One');
    }

    public function testItEditsAPrenote()
    {
        Livewire::test(PrenoteResource\Pages\EditPrenote::class, ['record' => 1])
            ->set('data.description', 'A Prenote')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'prenotes',
            [
                'description' => 'A Prenote',
            ]
        );
    }

    public function testItDoesntEditPrenoteIfIfDescriptionEmpty()
    {
        Livewire::test(PrenoteResource\Pages\EditPrenote::class, ['record' => 1])
            ->set('data.description', '')
            ->call('save')
            ->assertHasErrors(['data.description']);
    }

    public function testItDoesntEditPrenoteIfDescriptionTooLong()
    {
        Livewire::test(PrenoteResource\Pages\EditPrenote::class, ['record' => 1])
            ->set('data.description', Str::padRight('', 256, 'a'))
            ->call('save')
            ->assertHasErrors(['data.description']);
    }

    public function testItDisplaysControllers()
    {
        Livewire::test(
            PrenoteResource\RelationManagers\ControllersRelationManager::class,
            ['ownerRecord' => Prenote::findOrFail(1)]
        )->assertCanSeeTableRecords([1, 2]);
    }

    public function testControllersCanBeAttachedAtTheEnd()
    {
        Livewire::test(
            PrenoteResource\RelationManagers\ControllersRelationManager::class,
            ['ownerRecord' => Prenote::findOrFail(1)]
        )
            ->callTableAction('attach', Prenote::findOrFail(1), ['recordId' => 3])
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
            ],
            Prenote::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testControllersCanBeAttachedAfterAnotherController()
    {
        Livewire::test(
            PrenoteResource\RelationManagers\ControllersRelationManager::class,
            ['ownerRecord' => Prenote::findOrFail(1)]
        )
            ->callTableAction('attach', Prenote::findOrFail(1), ['recordId' => 3, 'insert_after' => 1])
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_S_TWR',
                'LON_S_CTR',
                'EGLL_N_APP',
            ],
            Prenote::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testControllersCanBeRemoved()
    {
        PrenoteService::setPositionsForPrenote(
            Prenote::findOrFail(1),
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ]
        );
        Livewire::test(
            PrenoteResource\RelationManagers\ControllersRelationManager::class,
            ['ownerRecord' => Prenote::findOrFail(1)]
        )
            ->callTableAction('detach', ControllerPosition::findOrFail(2))
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_S_TWR',
                'LON_S_CTR',
                'LON_C_CTR',
            ],
            Prenote::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testControllersCanBeRemovedAtTheEnd()
    {
        PrenoteService::setPositionsForPrenote(
            Prenote::findOrFail(1),
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ]
        );
        Livewire::test(
            PrenoteResource\RelationManagers\ControllersRelationManager::class,
            ['ownerRecord' => Prenote::findOrFail(1)]
        )
            ->callTableAction('detach', ControllerPosition::findOrFail(4))
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
            ],
            Prenote::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testControllersCanBeMovedUpTheOrder()
    {
        PrenoteService::setPositionsForPrenote(
            Prenote::findOrFail(1),
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ]
        );
        Livewire::test(
            PrenoteResource\RelationManagers\ControllersRelationManager::class,
            ['ownerRecord' => Prenote::findOrFail(1)]
        )
            ->callTableAction('moveUp', ControllerPosition::findOrFail(2))
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_N_APP',
                'EGLL_S_TWR',
                'LON_S_CTR',
                'LON_C_CTR',
            ],
            Prenote::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testControllersCanBeMovedUpTheOrderAtTheTop()
    {
        PrenoteService::setPositionsForPrenote(
            Prenote::findOrFail(1),
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ]
        );
        Livewire::test(
            PrenoteResource\RelationManagers\ControllersRelationManager::class,
            ['ownerRecord' => Prenote::findOrFail(1)]
        )
            ->callTableAction('moveUp', ControllerPosition::findOrFail(1))
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ],
            Prenote::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testControllersCanBeMovedDownTheOrder()
    {
        PrenoteService::setPositionsForPrenote(
            Prenote::findOrFail(1),
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ]
        );
        Livewire::test(
            PrenoteResource\RelationManagers\ControllersRelationManager::class,
            ['ownerRecord' => Prenote::findOrFail(1)]
        )
            ->callTableAction('moveDown', ControllerPosition::findOrFail(2))
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_S_TWR',
                'LON_S_CTR',
                'EGLL_N_APP',
                'LON_C_CTR',
            ],
            Prenote::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    public function testControllersCanBeMovedDownAtTheBottom()
    {
        PrenoteService::setPositionsForPrenote(
            Prenote::findOrFail(1),
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ]
        );
        Livewire::test(
            PrenoteResource\RelationManagers\ControllersRelationManager::class,
            ['ownerRecord' => Prenote::findOrFail(1)]
        )
            ->callTableAction('moveDown', ControllerPosition::findOrFail(4))
            ->assertHasNoTableActionErrors();

        $this->assertEquals(
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
                'LON_C_CTR',
            ],
            Prenote::findOrFail(1)
                ->controllers
                ->pluck('callsign')
                ->toArray()
        );
    }

    protected function getViewEditRecord(): Model
    {
        return Prenote::findOrFail(1);
    }

    protected function getResourceClass(): string
    {
        return PrenoteResource::class;
    }

    protected function getEditText(): string
    {
        return 'Edit Prenote One';
    }

    protected function getCreateText(): string
    {
        return 'Create prenote';
    }

    protected function getViewText(): string
    {
        return 'View Prenote One';
    }

    protected function getIndexText(): array
    {
        return ['Prenotes', 'Prenote One', 'Prenote Two'];
    }
}
